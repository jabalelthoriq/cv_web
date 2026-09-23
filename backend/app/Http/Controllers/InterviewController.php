<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InterviewController extends Controller
{
    public function submit(Request $request)
{
    if (!auth()->user()?->hasPlanAtLeast('pro')) {
        return response()->json([
            'status' => 'locked',
            'message' => 'Fitur interview AI membutuhkan plan Pro.'
        ], 403);
    }

    try {
        $cvId = $request->cv_id;
        $jobTema = $request->job_tema;
        $questions = $request->questions;
        $answers = $request->answers;
        $interviewIds = $request->interview_ids;

        $totalScore = 0;
        $feedbacks = [];

        foreach ($interviewIds as $index => $id) {
            $answer = $answers[$index] ?? '';

            // Panggil FastAPI untuk evaluate
            $response = \Http::timeout(30)->post(env('AI_API_URL', 'http://ai:8000') . '/evaluate-answer', [
                'interview_id' => $id,
                'answer' => $answer
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $totalScore += $data['score'] ?? 0;
                $feedbacks[] = $data['feedback'] ?? '';
            }
        }

        $finalScore = count($interviewIds) > 0
            ? round($totalScore / count($interviewIds))
            : 0;

        return response()->json([
            'status' => 'success',
            'score' => $finalScore,
            'feedback' => implode(" ", $feedbacks)
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
public function generate(Request $request)
{
    if (!auth()->user()?->hasPlanAtLeast('pro')) {
        return response()->json([
            'status' => 'locked',
            'message' => 'Fitur interview AI membutuhkan plan Pro.'
        ], 403);
    }

    $response = \Http::timeout(60)->post(env('AI_API_URL', 'http://ai:8000') . '/generate-interview', [
        'user_id' => auth()->id(),
        'cv_id' => $request->cv_id,
        'job_tema' => $request->job_tema,
    ]);

    return response()->json($response->json(), $response->status());
}

public function transcribe(Request $request)
{
    if (!auth()->user()?->hasPlanAtLeast('pro')) {
        return response()->json([
            'status' => 'locked',
            'message' => 'Fitur interview AI membutuhkan plan Pro.'
        ], 403);
    }

    $request->validate([
        'audio' => ['required', 'file', 'max:15360'],
    ]);

    $audio = $request->file('audio');

    try {
        $response = Http::timeout(120)
            ->attach(
                'file',
                file_get_contents($audio->getRealPath()),
                $audio->getClientOriginalName() ?: 'interview-answer.webm',
                ['Content-Type' => $audio->getMimeType() ?: 'audio/webm']
            )
            ->post(env('AI_API_URL', 'http://ai:8000') . '/transcribe-audio');

        return response()->json($response->json(), $response->status());
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal transkripsi audio: ' . $e->getMessage(),
        ], 500);
    }
}
}
