<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Cv;
use App\Models\Interview;
use App\Models\JobRecommendation;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

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
            'plan' => ucfirst($user->currentPlan()),
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
        $skillGap = $analysis['skill_gap'] ?? [];
        $semanticSimilarity = $analysis['semantic_similarity'] ?? [];
        $recommendationEngine = $analysis['recommendation_engine'] ?? [];

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
            'match_score' => $analysis['match_score'] ?? null,
            'readiness' => $analysis['readiness'] ?? null,
            'matched_skills' => $analysis['matched_skills'] ?? [],
            'missing_skills' => $analysis['missing_skills'] ?? [],
            'ats_breakdown' => $analysis['ats_breakdown'] ?? [],
            'ats_weights' => $analysis['ats_weights'] ?? [],
            'skill_gap' => $skillGap,
            'semantic_similarity' => $semanticSimilarity,
            'recommendation_engine' => $recommendationEngine,
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
        $jobsQuery = JobRecommendation::where('user_id', $user->id);

        if ($selectedCvId) {
            $jobsQuery->where('cv_id', $selectedCvId);
        } elseif ($latestCv) {
            $jobsQuery->where('cv_id', $latestCv->id);
        }

        $jobsRaw = $jobsQuery
            ->orderByDesc('match_score')
            ->take(12)
            ->get();

        if ($jobsRaw->isEmpty()) {
            $jobsRaw = JobRecommendation::where('user_id', $user->id)
                ->whereNull('cv_id')
                ->orderByDesc('match_score')
                ->take(12)
                ->get();
        }
        
        // ✅ Ambil job_tema dari selected CV
        $jobTemaFromCv = $selectedCv->job_tema ?? null;
        
        $cvSkills = $this->extractCvSkills($analysis);
        $jobData = $jobsRaw->map(function ($job) use ($jobTemaFromCv, $cvSkills) {
            $jobTitle = $job->job_title ?? '-';
            $requiredSkills = $this->requiredSkillsForJob($jobTitle, $job->job_tema ?? $jobTemaFromCv);
            $skillGap = $this->jobSkillGap($cvSkills, $requiredSkills);

            return [
                'id' => $job->id,
                'cv_id' => $job->cv_id,
                'job_title' => $jobTitle,
                'company' => $job->company ?? 'Unknown Company',
                'match_score' => $job->match_score ?? 0,
                'tags' => $this->extractTagsFromJob($jobTitle),
                'link' => $job->job_link ?? null,
                'job_tema' => $jobTemaFromCv,
                'required_skills' => array_values($requiredSkills),
                'matched_skills' => $skillGap['matched'],
                'missing_skills' => $skillGap['missing'],
            ];
        });

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
        $jobsForCv = JobRecommendation::where('user_id', $user->id)
            ->where('cv_id', $cv->id)
            ->orderByDesc('match_score')
            ->take(12)
            ->get();

        if ($jobsForCv->isEmpty()) {
            $jobsForCv = JobRecommendation::where('user_id', $user->id)
                ->whereNull('cv_id')
                ->orderByDesc('match_score')
                ->take(12)
                ->get();
        }

        $jobsByCv[$cv->id] = $jobsForCv
            ->map(fn($job) => [
                'id' => $job->id,
                'job_title' => $job->job_title,
                'company' => $job->company,
                'match_score' => $job->match_score,
                'job_link' => $job->job_link,
                'required_skills' => array_values($this->requiredSkillsForJob($job->job_title, $job->job_tema ?? $cv->job_tema)),
            ]);
    }
        $adminStats = null;
        $adminUsers = collect();
        $adminPayments = collect();
        $planChart = null;
        $loginChart = null;

        if ($user->role === 'admin') {
            $adminUsers = User::with('activeSubscription')->latest()->get();
            $adminPayments = Payment::with('user')->latest()->take(25)->get();
            $paid = Payment::where('status', 'paid')->count();
            $pending = Payment::where('status', 'pending')->count();
            $activeSubscriptions = Subscription::where('status', 'active')
                ->where('expired_at', '>', now())
                ->count();

            $adminStats = [
                'users' => User::count(),
                'payments' => Payment::count(),
                'paid' => $paid,
                'pending' => $pending,
                'active_subscriptions' => $activeSubscriptions,
            ];

            [$planChart, $loginChart] = $this->buildAdminCharts();
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
            'adminStats' => $adminStats,
            'adminUsers' => $adminUsers,
            'adminPayments' => $adminPayments,
            'planChart' => $planChart,
            'loginChart' => $loginChart,
        ]);
    }

    public function updateAdminUser(Request $request, User $targetUser)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($targetUser->id)],
            'role' => ['required', Rule::in(['user', 'admin'])],
            'plan' => ['required', Rule::in(['free', 'plus', 'pro'])],
        ]);

        $targetUser->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);

        $this->syncUserSubscription($targetUser, $data['plan']);

        return redirect()->route('dashboard', ['view' => 'admin'])
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function updateAdminPayment(Request $request, Payment $payment)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'order_id' => ['required', 'string', 'max:255', Rule::unique('payments', 'order_id')->ignore($payment->id)],
            'status' => ['required', Rule::in(['pending', 'paid', 'failed', 'expired', 'cancelled'])],
            'plan' => ['nullable', Rule::in(['free', 'plus', 'pro'])],
        ]);

        $payment->update([
            'order_id' => $data['order_id'],
            'status' => $data['status'],
            'plan' => $data['plan'] ?: $payment->plan,
            'paid_at' => $data['status'] === 'paid' ? ($payment->paid_at ?? now()) : $payment->paid_at,
        ]);

        if ($payment->user && $payment->type === 'subscription' && $data['status'] === 'paid') {
            $this->syncUserSubscription($payment->user, $data['plan'] ?: $payment->plan ?: 'plus');
        }

        return redirect()->route('dashboard', ['view' => 'admin'])
            ->with('success', 'Data pembayaran berhasil diperbarui.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);
    }

    private function syncUserSubscription(User $user, string $plan): void
    {
        if ($plan === 'free') {
            Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'expired',
                    'expired_at' => now(),
                ]);

            return;
        }

        Subscription::updateOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            [
                'plan' => $plan,
                'started_at' => now(),
                'expired_at' => now()->addMonth(),
            ]
        );
    }

    private function buildAdminCharts(): array
    {
        $months = collect(range(11, 0))->map(fn($back) => now()->startOfMonth()->subMonths($back));

        $labels = $months->map(fn(Carbon $month) => $month->translatedFormat('M y'))->values();

        $planSeries = [
            'free' => [],
            'plus' => [],
            'pro' => [],
        ];
        $loginSeries = [];

        foreach ($months as $month) {
            $planSeries['free'][] = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->whereDoesntHave('activeSubscription')
                ->count();

            foreach (['plus', 'pro'] as $plan) {
                $planSeries[$plan][] = Payment::where('type', 'subscription')
                    ->where('plan', $plan)
                    ->where('status', 'paid')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();
            }

            $loginSeries[] = User::whereNotNull('last_login_at')
                ->whereYear('last_login_at', $month->year)
                ->whereMonth('last_login_at', $month->month)
                ->count();
        }

        return [
            [
                'labels' => $labels,
                'series' => [
                    ['key' => 'free', 'label' => 'Free', 'color' => '#94a3b8', 'values' => $planSeries['free']],
                    ['key' => 'plus', 'label' => 'Plus', 'color' => '#00d4ff', 'values' => $planSeries['plus']],
                    ['key' => 'pro', 'label' => 'Pro', 'color' => '#8b5cf6', 'values' => $planSeries['pro']],
                ],
            ],
            [
                'labels' => $labels,
                'series' => [
                    ['key' => 'login', 'label' => 'User Login', 'color' => '#22d3ee', 'values' => $loginSeries],
                ],
            ],
        ];
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

    private function extractCvSkills(array $analysis): array
    {
        return collect($analysis['matched_skills'] ?? [])
            ->merge($analysis['bilingual']['canonical_skills'] ?? [])
            ->merge($analysis['skill_gap']['matched_skills'] ?? [])
            ->map(fn($skill) => strtolower((string) $skill))
            ->flatMap(fn($skill) => $this->expandSkillAlias($skill))
            ->unique()
            ->values()
            ->all();
    }

    private function requiredSkillsForJob(?string $jobTitle, ?string $jobTema = null): array
    {
        $text = strtolower(($jobTitle ?? '') . ' ' . ($jobTema ?? ''));
        $skills = [];

        if (str_contains($text, 'android')) {
            $skills = array_merge($skills, ['Android', 'Java', 'Kotlin', 'REST API']);
        }
        if (str_contains($text, 'flutter') || (str_contains($text, 'mobile') && !str_contains($text, 'android'))) {
            $skills = array_merge($skills, ['Flutter', 'Dart', 'Mobile UI', 'REST API']);
        }
        if (str_contains($text, 'backend') || str_contains($text, 'laravel')) {
            $skills = array_merge($skills, ['Laravel', 'PHP', 'MySQL', 'REST API']);
        }
        if (str_contains($text, 'frontend') || str_contains($text, 'react')) {
            $skills = array_merge($skills, ['React', 'JavaScript', 'HTML CSS', 'UI UX']);
        }
        if (str_contains($text, 'data analyst')) {
            $skills = array_merge($skills, ['Data Analysis', 'SQL', 'Excel']);
        }
        if (str_contains($text, 'ai') || str_contains($text, 'machine learning')) {
            $skills = array_merge($skills, ['Python', 'Machine Learning', 'Data Analysis']);
        }
        if (str_contains($text, 'ui ux') || str_contains($text, 'designer')) {
            $skills = array_merge($skills, ['UI UX', 'Figma', 'Communication']);
        }
        if (str_contains($text, 'marketing')) {
            $skills = array_merge($skills, ['Marketing', 'Communication', 'Data Analysis']);
        }

        return array_values(array_unique($skills ?: ['Communication', 'Teamwork']));
    }

    private function jobSkillGap(array $cvSkills, array $requiredSkills): array
    {
        $normalizedCvSkills = collect($cvSkills)
            ->map(fn($skill) => $this->normalizeSkillLabel($skill))
            ->unique();

        $matched = [];
        $missing = [];

        foreach ($requiredSkills as $skill) {
            $requiredAliases = $this->expandSkillAlias($this->normalizeSkillLabel($skill));
            $hasSkill = collect($requiredAliases)
                ->contains(fn($alias) => $normalizedCvSkills->contains($this->normalizeSkillLabel($alias)));

            if ($hasSkill) {
                $matched[] = $skill;
            } else {
                $missing[] = $skill;
            }
        }

        return [
            'matched' => array_values(array_unique($matched)),
            'missing' => array_values(array_unique($missing)),
        ];
    }

    private function expandSkillAlias(string $skill): array
    {
        $skill = $this->normalizeSkillLabel($skill);
        $aliases = [
            'api' => ['api', 'rest api', 'rest'],
            'database' => ['database', 'mysql', 'sql'],
            'html css' => ['html css', 'html_css', 'html', 'css'],
            'javascript' => ['javascript', 'js', 'nextjs', 'next.js'],
            'laravel' => ['laravel', 'php'],
            'mobile' => ['mobile', 'mobile_development', 'android', 'flutter'],
            'mobile development' => ['mobile', 'mobile_development', 'android', 'flutter'],
            'mobile_development' => ['mobile', 'mobile_development', 'android', 'flutter'],
            'react' => ['react', 'reactjs', 'nextjs', 'next.js'],
            'rest api' => ['api', 'rest api', 'rest'],
            'sql' => ['database', 'mysql', 'sql'],
            'ui ux' => ['ui ux', 'ui_ux', 'figma', 'design'],
            'ui_ux' => ['ui ux', 'ui_ux', 'figma', 'design'],
        ];

        return $aliases[$skill] ?? [$skill];
    }

    private function normalizeSkillLabel(string $skill): string
    {
        return str_replace(['_', '/', '-'], ' ', strtolower(trim($skill)));
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
