<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Interview;

class InterviewSeeder extends Seeder
{
    public function run(): void
    {
        Interview::create([
            'user_id' => 1,
            'question' => 'Ceritakan tentang diri Anda',
            'answer' => 'Saya adalah mahasiswa IT yang memiliki minat di bidang web development dan AI.',
            'score' => 85,
            'feedback' => 'Jawaban cukup jelas, namun bisa ditambahkan pengalaman konkret.'
        ]);

        Interview::create([
            'user_id' => 1,
            'question' => 'Apa kelebihan Anda?',
            'answer' => 'Saya cepat belajar dan mampu bekerja dalam tim.',
            'score' => 80,
            'feedback' => 'Jawaban baik, tetapi perlu contoh nyata.'
        ]);

        Interview::create([
            'user_id' => 1,
            'question' => 'Apa kelemahan Anda?',
            'answer' => 'Saya terkadang terlalu fokus pada detail.',
            'score' => 75,
            'feedback' => 'Jawaban cukup aman, bisa ditambahkan cara mengatasinya.'
        ]);
    }
}