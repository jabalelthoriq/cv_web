<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Cv;
use App\Models\Interview;
use App\Models\JobRecommendation;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // =========================
        // DATA PENGGUNA
        // =========================
        $userData = [
            'name' => $user->name,
            'plan' => $user->role === 'admin' ? 'Pro' : 'Free',
            'avatar' => $user->avatar,
        ];

        // Profile completion
        $profileCompletion = $this->calculateProfileCompletion($user);

        // =========================
        // CV ANALYSIS
        // =========================
        $latestCv = Cv::where('user_id', $user->id)->latest()->first();
        $previousCv = Cv::where('user_id', $user->id)->latest()->skip(1)->first();

        $cvAnalysis = [
            'score' => $latestCv->score ?? 0,
            'delta' => $latestCv && $previousCv
                ? $latestCv->score - $previousCv->score
                : 0,
            'ats_passed' => ($latestCv && isset($latestCv->analysis['strengths']))
                ? count($latestCv->analysis['strengths'])
                : 0,
            'strengths' => $latestCv->analysis['strengths'] ?? [],
            'weaknesses' => $latestCv->analysis['weaknesses'] ?? [],
            'suggestions' => $latestCv->analysis['suggestions'] ?? [],
        ];

        // =========================
        // INTERVIEW
        // =========================
        $interviews = Interview::where('user_id', $user->id)->get();
        
        $totalSessions = $interviews->count();
        $avgScore = $interviews->avg('score') ?? 0;
        
        $breakdown = $this->calculateInterviewBreakdown($interviews);

        $interviewData = [
            'total_sessions' => $totalSessions,
            'average_score' => round($avgScore, 2),
            'breakdown' => $breakdown,
            'recent_interviews' => $interviews->take(5)->map(function($interview) {
                return [
                    'question' => $interview->question,
                    'answer' => $interview->answer,
                    'score' => $interview->score,
                    'feedback' => $interview->feedback,
                ];
            }),
        ];

        // =========================
        // JOB RECOMMENDATION
        // =========================
        $jobs = JobRecommendation::where('user_id', $user->id)->get();

        $jobData = $jobs->map(function ($job) {
            $tags = $this->extractTagsFromJob($job->job_title);
            
            return [
                'job_title' => $job->job_title,
                'company' => $job->company,
                'match_score' => $job->match_score,
                'tags' => $tags,
                'link' => $job->job_link,
            ];
        });

        // =========================
        // ACTIVITY FEED
        // =========================
        $activities = $this->getUserActivities($user);

        // Return view dengan data
        return view('dashboard', [
            'userData' => $userData,
            'profileCompletion' => $profileCompletion,
            'cvAnalysis' => $cvAnalysis,
            'interviewData' => $interviewData,
            'jobData' => $jobData,
            'activities' => $activities,
        ]);
    }
    
    private function calculateProfileCompletion($user)
    {
        $completion = [
            'personal_info' => ($user->name && $user->email) ? 100 : 0,
            'experience' => 0,
            'education' => 0,
            'skills' => 0,
            'portfolio' => $user->avatar ? 50 : 0,
        ];
        
        if (Cv::where('user_id', $user->id)->exists()) {
            $completion['personal_info'] = 100;
            $completion['portfolio'] += 50;
        }
        
        return $completion;
    }
    
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
        
        $counts = [
            'confidence' => 0,
            'technical' => 0,
            'communication' => 0,
            'problem_solving' => 0,
        ];
        
        foreach ($interviews as $interview) {
            $question = strtolower($interview->question);
            
            if (str_contains($question, 'kelebihan') || str_contains($question, 'kelemahan')) {
                $breakdown['confidence'] += $interview->score;
                $counts['confidence']++;
            } elseif (str_contains($question, 'teknis') || str_contains($question, 'programming')) {
                $breakdown['technical'] += $interview->score;
                $counts['technical']++;
            } elseif (str_contains($question, 'ceritakan') || str_contains($question, 'tim')) {
                $breakdown['communication'] += $interview->score;
                $counts['communication']++;
            } else {
                $breakdown['problem_solving'] += $interview->score;
                $counts['problem_solving']++;
            }
        }
        
        foreach ($breakdown as $key => $value) {
            if ($counts[$key] > 0) {
                $breakdown[$key] = round($value / $counts[$key]);
            }
        }
        
        return $breakdown;
    }
    
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
        
        foreach ($keywords as $key => $keywordTags) {
            if (str_contains($jobTitle, $key)) {
                $tags = array_merge($tags, $keywordTags);
            }
        }
        
        return array_unique($tags);
    }
    
    private function getUserActivities($user)
    {
        $activities = collect();
        
        $cvs = Cv::where('user_id', $user->id)->latest()->take(3)->get();
        foreach ($cvs as $cv) {
            $activities->push([
                'type' => 'cv_upload',
                'message' => 'Upload CV: ' . basename($cv->file_path),
                'time' => $cv->created_at,
            ]);
        }
        
        $interviews = Interview::where('user_id', $user->id)->latest()->take(3)->get();
        foreach ($interviews as $interview) {
            $activities->push([
                'type' => 'interview',
                'message' => 'Selesai simulasi interview: ' . $interview->question,
                'time' => $interview->created_at,
            ]);
        }
        
        $jobs = JobRecommendation::where('user_id', $user->id)->latest()->take(3)->get();
        foreach ($jobs as $job) {
            $activities->push([
                'type' => 'job_match',
                'message' => 'Mendapat rekomendasi job: ' . $job->job_title,
                'time' => $job->created_at,
            ]);
        }
        
        return $activities->sortByDesc('time')->values();
    }
}