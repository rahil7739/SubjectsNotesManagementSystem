<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SubjectsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password'],
        );

        $subjects = [
            [
                'name' => 'Biology',
                'description' => 'Cell structure, genetics, and living systems.',
            ],
            [
                'name' => 'Mathematics',
                'description' => 'Algebra, equations, and problem-solving.',
            ],
            [
                'name' => 'World History',
                'description' => 'Key events, societies, and historical change.',
            ],
            [
                'name' => 'Physics',
                'description' => 'Motion, forces, energy, and measurement.',
            ],
            [
                'name' => 'English Literature',
                'description' => 'Reading, interpretation, and written analysis.',
            ],
        ];

        foreach ($subjects as $subject) {
            $user->subjects()->firstOrCreate(
                ['name' => $subject['name']],
                ['description' => $subject['description']],
            );
        }
    }
}
