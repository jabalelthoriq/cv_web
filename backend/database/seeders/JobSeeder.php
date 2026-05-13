<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobRecommendation;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        JobRecommendation::create([
            'user_id' => 1,
            'job_title' => 'Backend Developer (Laravel)',
            'company' => 'PT Teknologi Nusantara',
            'match_score' => 92,
            'job_link' => 'https://example.com/jobs/backend-laravel',
        ]);

        JobRecommendation::create([
            'user_id' => 1,
            'job_title' => 'Frontend Developer (React)',
            'company' => 'Startup Digital Indonesia',
            'match_score' => 88,
            'job_link' => 'https://example.com/jobs/frontend-react',
        ]);

        JobRecommendation::create([
            'user_id' => 1,
            'job_title' => 'AI Engineer (NLP)',
            'company' => 'AI Labs Indonesia',
            'match_score' => 85,
            'job_link' => 'https://example.com/jobs/ai-engineer',
        ]);
    }
}