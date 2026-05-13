<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cv;

class CvSeeder extends Seeder
{
    public function run(): void
    {
        Cv::create([
            'user_id' => 1, // User 1
            'file_path' => 'cv/user1_cv.pdf',
            'score' => 85,
            'analysis' => [
                'strengths' => [
                    'Good communication skills',
                    'Relevant experience in IT'
                ],
                'weaknesses' => [
                    'Lack of certifications'
                ],
                'suggestions' => [
                    'Add more projects',
                    'Include certifications'
                ]
            ],
        ]);

        // Optional: tambah beberapa data dummy
        Cv::create([
            'user_id' => 1,
            'file_path' => 'cv/user1_cv_v2.pdf',
            'score' => 78,
            'analysis' => [
                'strengths' => ['Teamwork'],
                'weaknesses' => ['Short experience'],
                'suggestions' => ['Improve portfolio']
            ],
        ]);
    }
}