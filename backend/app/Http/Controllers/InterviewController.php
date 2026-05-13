<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function submit(Request $request)
{
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
            $response = \Http::post('http://127.0.0.1:8004/evaluate-answer', [
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
}
