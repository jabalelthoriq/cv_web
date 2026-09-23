<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\JobRecommendation;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PremiumResultTest extends TestCase
{
    use RefreshDatabase;

    public function test_premium_result_shows_only_highest_scored_job(): void
    {
        $user = User::factory()->create();

        $cv = Cv::create([
            'user_id' => $user->id,
            'file_path' => 'cv_uploads/sample.pdf',
            'score' => 88,
            'analysis' => [
                'strengths' => ['Strong Laravel experience'],
                'weaknesses' => ['Portfolio link belum jelas'],
                'suggestions' => ['Tambahkan metrik proyek'],
            ],
            'job_tema' => 'Backend Developer',
        ]);

        JobRecommendation::create([
            'user_id' => $user->id,
            'cv_id' => $cv->id,
            'job_title' => 'Frontend Developer',
            'company' => 'Low Score Company',
            'match_score' => 72,
            'job_link' => 'https://example.com/frontend',
            'job_tema' => 'Frontend',
        ]);

        JobRecommendation::create([
            'user_id' => $user->id,
            'cv_id' => $cv->id,
            'job_title' => 'Backend Engineer',
            'company' => 'High Score Company',
            'match_score' => 94,
            'job_link' => 'https://example.com/backend',
            'job_tema' => 'Backend Developer',
        ]);

        $payment = Payment::create([
            'order_id' => 'CV-TEST-1',
            'user_id' => $user->id,
            'cv_id' => $cv->id,
            'type' => 'cv',
            'status' => 'paid',
        ]);

        $url = URL::temporarySignedRoute(
            'premium.result',
            now()->addMinutes(15),
            ['payment' => $payment->id],
            false
        );

        $this->get($url)
            ->assertOk()
            ->assertSee('Backend Engineer')
            ->assertSee('94%')
            ->assertSee('High Score Company')
            ->assertDontSee('Frontend Developer')
            ->assertDontSee('Low Score Company');
    }

    public function test_premium_result_falls_back_to_highest_user_job_when_cv_id_is_missing(): void
    {
        $user = User::factory()->create();

        $cv = Cv::create([
            'user_id' => $user->id,
            'file_path' => 'cv_uploads/legacy.pdf',
            'score' => 81,
            'analysis' => [],
            'job_tema' => 'Web Developer',
        ]);

        JobRecommendation::create([
            'user_id' => $user->id,
            'cv_id' => null,
            'job_title' => 'Junior Web Developer',
            'company' => 'Legacy Jobs',
            'match_score' => 91,
            'job_link' => 'https://example.com/web',
            'job_tema' => 'Web Developer',
        ]);

        JobRecommendation::create([
            'user_id' => $user->id,
            'cv_id' => null,
            'job_title' => 'Data Analyst',
            'company' => 'Lower Legacy Jobs',
            'match_score' => 70,
            'job_link' => 'https://example.com/data',
            'job_tema' => 'Data',
        ]);

        $payment = Payment::create([
            'order_id' => 'CV-TEST-LEGACY',
            'user_id' => $user->id,
            'cv_id' => $cv->id,
            'type' => 'cv',
            'status' => 'paid',
        ]);

        $url = URL::temporarySignedRoute(
            'premium.result',
            now()->addMinutes(15),
            ['payment' => $payment->id],
            false
        );

        $this->get($url)
            ->assertOk()
            ->assertSee('Junior Web Developer')
            ->assertSee('91%')
            ->assertDontSee('Data Analyst')
            ->assertDontSee('Lowongan belum tersedia');
    }
}
