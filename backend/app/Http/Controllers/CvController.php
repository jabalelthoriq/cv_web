<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\JobRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CvController extends Controller
{
    public function uploadCV(Request $request)
{
    $request->validate([
        'cv_file' => 'required|mimes:pdf|max:2048',
    ]);

    try {
        $file = $request->file('cv_file');

        $userId = auth()->id(); // null kalau guest
        $sessionId = session()->getId();

        // Hitung jumlah CV (user atau guest)
        $count = Cv::when($userId, function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->when(!$userId, function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
            })
            ->count();

        // Nama file unik (hindari overwrite)
        $fileName = 'cv_' . time() . '_' . ($count + 1) . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('cv_uploads', $fileName, 'public');

        $cv = Cv::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'file_path' => $path,
            'score' => null, // ❗ penting: null = belum selesai
            'analysis' => [
                'status' => 'processing',
                'message' => 'AI sedang menganalisis...'
            ],
        ]);

        // FastAPI hanya mengembalikan JSON. Laravel tetap menyimpan hasil ke database.
        try {
            $aiResponse = Http::timeout(45)->post(env('AI_API_URL', 'http://ai:8000') . '/analyze', [
                'cv_id' => $cv->id,
                'file_path' => $path,
            ]);

            if ($aiResponse->successful()) {
                $aiResult = $aiResponse->json();
                $analysis = $aiResult['analysis'] ?? [
                    'status' => 'completed',
                    'message' => 'Analisis selesai.',
                ];
                $jobTema = $this->detectJobTema($analysis);

                $cv->update([
                    'score' => $aiResult['score'] ?? 0,
                    'analysis' => $analysis,
                    'job_tema' => $jobTema,
                ]);

                $this->syncJobRecommendations($cv, $analysis, $jobTema);
            } else {
                $cv->update([
                    'score' => 0,
                    'analysis' => [
                        'status' => 'error',
                        'message' => 'AI gagal menganalisis CV.',
                        'detail' => $aiResponse->json('detail') ?? $aiResponse->body(),
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('FastAPI error: ' . $e->getMessage());
            $cv->update([
                'score' => 0,
                'analysis' => [
                    'status' => 'error',
                    'message' => 'AI service belum tersedia.',
                    'detail' => $e->getMessage(),
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $cv
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

private function detectJobTema(array $analysis): string
{
    $skills = collect($analysis['matched_skills'] ?? [])
        ->merge($analysis['bilingual']['canonical_skills'] ?? [])
        ->map(fn($skill) => strtolower((string) $skill))
        ->unique()
        ->values();

    if ($skills->intersect(['laravel', 'php', 'database', 'api', 'nodejs'])->isNotEmpty()) {
        return 'Backend Developer';
    }
    if ($skills->intersect(['react', 'ui_ux', 'figma'])->isNotEmpty()) {
        return 'Frontend / UI UX';
    }
    if ($skills->intersect(['flutter', 'mobile_development'])->isNotEmpty()) {
        return 'Mobile Developer';
    }
    if ($skills->intersect(['data_analysis', 'machine_learning'])->isNotEmpty()) {
        return 'Data / AI';
    }
    if ($skills->intersect(['marketing', 'communication'])->isNotEmpty()) {
        return 'Digital Marketing';
    }

    return 'General Entry Level';
}

private function syncJobRecommendations(Cv $cv, array $analysis, string $jobTema): void
{
    if (!$cv->user_id) {
        return;
    }

    $skills = collect($analysis['matched_skills'] ?? [])
        ->merge($analysis['bilingual']['canonical_skills'] ?? [])
        ->map(fn($skill) => strtolower((string) $skill))
        ->unique()
        ->values();

    $catalog = $this->jobCatalog();
    $ranked = collect($catalog)
        ->map(function (array $job) use ($skills, $analysis, $jobTema) {
            $required = collect($job['skills']);
            $hits = $required->intersect($skills)->count();
            $baseScore = (int) ($analysis['match_score'] ?? $analysis['score'] ?? 60);
            $skillScore = $required->isEmpty() ? 0 : round(($hits / $required->count()) * 35);
            $themeBonus = $job['theme'] === $jobTema ? 10 : 0;

            return array_merge($job, [
                'match_score' => min(98, max(55, round(($baseScore * 0.55) + $skillScore + $themeBonus))),
            ]);
        })
        ->sortByDesc('match_score')
        ->take(8)
        ->values();

    JobRecommendation::where('cv_id', $cv->id)->delete();

    foreach ($ranked as $job) {
        JobRecommendation::create([
            'user_id' => $cv->user_id,
            'cv_id' => $cv->id,
            'job_title' => $job['title'],
            'company' => $job['company'],
            'match_score' => $job['match_score'],
            'job_link' => $job['link'],
            'job_tema' => $jobTema,
        ]);
    }
}

private function jobCatalog(): array
{
    return [
        [
            'title' => 'Backend Developer (Laravel)',
            'company' => 'Jobstreet Indonesia',
            'theme' => 'Backend Developer',
            'skills' => ['laravel', 'php', 'database', 'api'],
            'link' => 'https://id.jobstreet.com/php-laravel-developer-jobs',
        ],
        [
            'title' => 'Junior Web Developer',
            'company' => 'Jobstreet Indonesia',
            'theme' => 'Backend Developer',
            'skills' => ['laravel', 'php', 'react', 'database'],
            'link' => 'https://id.jobstreet.com/junior-web-developer-jobs',
        ],
        [
            'title' => 'Frontend Developer (React)',
            'company' => 'Glints Indonesia',
            'theme' => 'Frontend / UI UX',
            'skills' => ['react', 'ui_ux', 'figma'],
            'link' => 'https://glints.com/id/en/find-jobs/loker-react-developer',
        ],
        [
            'title' => 'Mobile Developer (Flutter)',
            'company' => 'Jobstreet Indonesia',
            'theme' => 'Mobile Developer',
            'skills' => ['flutter', 'mobile_development'],
            'link' => 'https://id.jobstreet.com/flutter-developer-jobs',
        ],
        [
            'title' => 'Android Developer (Java/Kotlin)',
            'company' => 'Jobstreet Indonesia',
            'theme' => 'Mobile Developer',
            'skills' => ['android', 'java', 'kotlin', 'api'],
            'link' => 'https://id.jobstreet.com/android-developer-jobs',
        ],
        [
            'title' => 'Data Analyst Intern',
            'company' => 'Jobstreet Indonesia',
            'theme' => 'Data / AI',
            'skills' => ['data_analysis', 'database', 'python'],
            'link' => 'https://id.jobstreet.com/data-analyst-internship-jobs',
        ],
        [
            'title' => 'AI Engineer Intern',
            'company' => 'Glints Indonesia',
            'theme' => 'Data / AI',
            'skills' => ['machine_learning', 'python', 'data_analysis'],
            'link' => 'https://glints.com/id/en/find-jobs/loker-ai-engineer',
        ],
        [
            'title' => 'UI UX Designer Intern',
            'company' => 'Glints Indonesia',
            'theme' => 'Frontend / UI UX',
            'skills' => ['ui_ux', 'figma', 'communication'],
            'link' => 'https://glints.com/id/en/find-jobs/loker-ui-ux-designer',
        ],
        [
            'title' => 'Digital Marketing Staff',
            'company' => 'Jobstreet Indonesia',
            'theme' => 'Digital Marketing',
            'skills' => ['marketing', 'communication', 'data_analysis'],
            'link' => 'https://id.jobstreet.com/digital-marketing-jobs',
        ],
    ];
}
}
