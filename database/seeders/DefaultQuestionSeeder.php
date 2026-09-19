<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;

class DefaultQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            // ================= IQ Questions =================
            [
                'question_text' => 'What comes next in the sequence: 2, 4, 8, 16, ...?',
                'type' => 'mcq',
                'options' => json_encode(['24', '32', '64', '18']),
                'category' => 'iq',
                'department' => null,
            ],
            
            // ================= Departmental: Video Editor =================
            [
                'question_text' => 'Which keyboard shortcut is used to "Cut" or "Razor" a clip in Premiere Pro?',
                'type' => 'mcq',
                'options' => json_encode(['V', 'C', 'B', 'M']),
                'category' => 'departmental',
                'department' => 'Video Editor',
            ],
            [
                'question_text' => 'What is the standard frame rate for cinematic videos?',
                'type' => 'mcq',
                'options' => json_encode(['24 fps', '30 fps', '60 fps', '120 fps']),
                'category' => 'departmental',
                'department' => 'Video Editor',
            ],

            // ================= Departmental: Graphic Designer =================
            [
                'question_text' => 'Which color mode is strictly used for Print Media?',
                'type' => 'mcq',
                'options' => json_encode(['RGB', 'CMYK', 'HEX', 'HSL']),
                'category' => 'departmental',
                'department' => 'Graphic Designer',
            ],

            // ================= Office Rules =================
            [
                'question_text' => 'Do you agree to maintain the standard office check-in time of 9:00 AM?',
                'type' => 'boolean',
                'options' => json_encode(['Yes', 'No']),
                'category' => 'office_rules',
                'department' => null,
            ],
            [
                'question_text' => 'Are you comfortable with our Non-Disclosure Agreement (NDA)?',
                'type' => 'boolean',
                'options' => json_encode(['Yes', 'No']),
                'category' => 'office_rules',
                'department' => null,
            ],
        ];

        foreach ($questions as $q) {
            Question::firstOrCreate(['question_text' => $q['question_text']], $q);
        }
    }
}