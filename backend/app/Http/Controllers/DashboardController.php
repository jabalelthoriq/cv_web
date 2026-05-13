<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cv;
use App\Models\Interview;
use App\Models\JobRecommendation;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $user = Auth::user();

        // =========================
        // USER
        // =========================
        $userData = [
            'name' => $user->name,
            'plan' => $user->role === 'admin' ? 'Pro' : 'Free',
            'avatar' => $user->avatar,
        ];

        // =========================
        // CV
        // =========================
        $cvs = Cv::where('user_id', $user->id)->latest()->get();

        $latestCv = $cvs->first();
        $previousCv = $cvs->skip(1)->first();

        // ✅ FIX: hanya set sekali
        $selectedCvId = request()->query('cv_id');
        if (!$selectedCvId && $latestCv) {
            $selectedCvId = $latestCv->id;
        }

        // ✅ TAMBAHKAN: Ambil selected CV berdasarkan ID
        $selectedCv = null;
        if ($selectedCvId) {
            $selectedCv = $cvs->where('id', $selectedCvId)->first();
        }
        if (!$selectedCv) {
            $selectedCv = $latestCv;
        }

        // =========================
        // SAFE ANALYSIS
        // =========================
        $analysis = [];

        // ✅ Gunakan selectedCv, bukan latestCv
        if ($selectedCv && !empty($selectedCv->analysis)) {
            $decoded = is_array($selectedCv->analysis)
                ? $selectedCv->analysis
                : json_decode($selectedCv->analysis, true);

            $analysis = is_array($decoded) ? $decoded : [];
        }

        $strengths   = $analysis['strengths'] ?? [];
        $weaknesses  = $analysis['weaknesses'] ?? [];
        $suggestions = $analysis['suggestions'] ?? [];

        // =========================
        // CV ANALYSIS
        // =========================
        $cvAnalysis = [
            'score' => $selectedCv->score ?? 0,  // ✅ Gunakan selectedCv
            'delta' => ($selectedCv && $previousCv)
                ? ($selectedCv->score - $previousCv->score)
                : 0,
            'ats_passed' => count($strengths),
            'total_cv' => $cvs->count(),
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'suggestions' => $suggestions,
            'job_tema' => $selectedCv->job_tema ?? null,  // ✅ Sekarang $selectedCv sudah ada
        ];

        // =========================
        // INTERVIEW
        // =========================
        $interviews = Interview::where('user_id', $user->id)->get();

        $interviewData = [
            'total_sessions' => $interviews->count(),
            'average_score' => round($interviews->avg('score') ?? 0, 2),
            'breakdown' => $this->calculateInterviewBreakdown($interviews),
            'recent_interviews' => $interviews->take(5)->map(fn($i) => [
                'question' => $i->question,
                'answer' => $i->answer,
                'score' => $i->score,
                'feedback' => $i->feedback,
            ]),
        ];

        // =========================
        // JOBS (FIXED & CLEAN)
        // =========================
        $jobsQuery = JobRecommendation::query();    

        if ($selectedCvId) {
            $jobsQuery->where('cv_id', $selectedCvId);
        } elseif ($latestCv) {
            $jobsQuery->where('cv_id', $latestCv->id);
        }

        $jobsRaw = $jobsQuery
            ->orderByDesc('match_score')
            ->take(12)
            ->get();
        
        // ✅ Ambil job_tema dari selected CV
        $jobTemaFromCv = $selectedCv->job_tema ?? null;
        
        $jobData = $jobsRaw->map(fn($job) => [
            'id' => $job->id,
            'cv_id' => $job->cv_id,
            'job_title' => $job->job_title ?? '-',
            'company' => $job->company ?? 'Unknown Company',
            'match_score' => $job->match_score ?? 0,
            'tags' => $this->extractTagsFromJob($job->job_title ?? ''),
            'link' => $job->job_link ?? null,
            'job_tema' => $jobTemaFromCv,  // ✅ job_tema dari CV, bukan dari job_recommendations
        ]);

        // =========================
        // ACTIVITIES
        // =========================
        $activities = $this->getUserActivities($user);

        // =========================
        // PROFILE AI SUMMARY
        // =========================
        $profileFromAI = [
            'strengths' => min(count($strengths) * 20, 100),
            'weaknesses' => min(count($weaknesses) * 20, 100),
            'suggestions' => min(count($suggestions) * 20, 100),
        ];

        // =========================
        // VIEW STATE
        // =========================
        $selectedView = request()->query('view', 'dashboard');


         // =========================
        // AMBIL SEMUA JOB DI CV
        // =========================
        $allCvs = Cv::where('user_id', $user->id)
        ->select('id', 'file_path', 'score', 'job_tema')
        ->latest()
        ->get();
        $jobsByCv = [];
    foreach ($allCvs as $cv) {

    // =========================
        // JOB PERCV
        // =========================
        $jobsByCv[$cv->id] = JobRecommendation::where('cv_id', $cv->id)
            ->where('cv_id', $cv->id)
            ->orderByDesc('match_score')
            ->take(12)
            ->get()
            ->map(fn($job) => [
                'id' => $job->id,
                'job_title' => $job->job_title,
                'company' => $job->company,
                'match_score' => $job->match_score,
                'job_link' => $job->job_link,
            ]);
    }
        // =========================
        // RETURN VIEW
        // =========================
        return view('dashboard', [
            'userData' => $userData,
            'cvAnalysis' => $cvAnalysis,
            'profileFromAI' => $profileFromAI,
            'interviewData' => $interviewData,
            'jobData' => $jobData,
            'activities' => $activities,
            'latestCv' => $latestCv,
            'selectedView' => $selectedView,
            'cvs' => $cvs,
            'selectedCvId' => $selectedCvId,
            'selectedCv' => $selectedCv,
            'allCvs' => $allCvs,  
            'jobsByCv' => $jobsByCv,
        ]);
    }

    // =========================
    // INTERVIEW BREAKDOWN
    // =========================
    private function calculateInterviewBreakdown($interviews)
    {
        if ($interviews->isEmpty()) {
            return [
                'confidence' => 0,
                'technical' => 0,
                'communication' => 0,
                'problem_solving' => 0,
            ];
        }

        $breakdown = [
            'confidence' => 0,
            'technical' => 0,
            'communication' => 0,
            'problem_solving' => 0,
        ];

        $counts = $breakdown;

        foreach ($interviews as $interview) {
            $q = strtolower($interview->question ?? '');

            if (str_contains($q, 'kelebihan') || str_contains($q, 'kelemahan')) {
                $breakdown['confidence'] += $interview->score;
                $counts['confidence']++;
            } elseif (str_contains($q, 'teknis') || str_contains($q, 'programming')) {
                $breakdown['technical'] += $interview->score;
                $counts['technical']++;
            } elseif (str_contains($q, 'ceritakan') || str_contains($q, 'tim')) {
                $breakdown['communication'] += $interview->score;
                $counts['communication']++;
            } else {
                $breakdown['problem_solving'] += $interview->score;
                $counts['problem_solving']++;
            }
        }

        foreach ($breakdown as $key => $val) {
            if ($counts[$key] > 0) {
                $breakdown[$key] = round($val / $counts[$key]);
            }
        }

        return $breakdown;
    }

    // =========================
    // TAG EXTRACTION
    // =========================
    private function extractTagsFromJob($jobTitle)
    {
        $tags = [];

        $keywords = [
            'Laravel' => ['Laravel', 'Backend'],
            'React' => ['React', 'Frontend'],
            'AI' => ['AI', 'Machine Learning'],
            'NLP' => ['NLP', 'AI'],
            'Developer' => ['Programming'],
        ];

        foreach ($keywords as $key => $val) {
            if (str_contains($jobTitle, $key)) {
                $tags = array_merge($tags, $val);
            }
        }

        return array_unique($tags);
    }

    // =========================
    // USER ACTIVITIES
    // =========================
    private function getUserActivities($user)
    {
        return collect()
            ->merge(
                Cv::where('user_id', $user->id)->latest()->take(3)->get()->map(fn($cv) => [
                    'type' => 'cv_upload',
                    'message' => 'Upload CV: ' . ($cv->file_path ? basename($cv->file_path) : 'Unknown File'),
                    'time' => $cv->created_at ?? now(),
                ])
            )
            ->merge(
                Interview::where('user_id', $user->id)->latest()->take(3)->get()->map(fn($i) => [
                    'type' => 'interview',
                    'message' => 'Selesai interview',
                    'time' => $i->created_at ?? now(),
                ])
            )
            ->merge(
                JobRecommendation::query()->latest()->take(3)->get()->map(fn($j) => [
                    'type' => 'job_match',
                    'message' => 'Job: ' . ($j->job_title ?? 'Unknown Job'),
                    'time' => $j->created_at ?? now(),
                ])
            )
            ->filter(fn($item) => !empty($item['time']))
            ->sortByDesc('time')
            ->values();
    }
}