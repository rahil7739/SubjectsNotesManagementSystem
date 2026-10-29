<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class NotesSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->firstOrFail();

        $notes = [
            [
                'subject' => 'Biology',
                'title' => 'Cell structure and function',
                'body' => 'Cells are the basic structural and functional units of living organisms. Review the roles of the nucleus, membrane, cytoplasm, and mitochondria.',
            ],
            [
                'subject' => 'Mathematics',
                'title' => 'Linear equations',
                'body' => 'To solve a linear equation, isolate the variable by applying the same operation to both sides. Check the result by substituting it into the original equation.',
            ],
            [
                'subject' => 'World History',
                'title' => 'Understanding primary sources',
                'body' => 'When evaluating a primary source, identify its creator, date, purpose, intended audience, and historical context. Compare it with other evidence.',
            ],
            [
                'subject' => 'Physics',
                'title' => 'Newton\'s laws of motion',
                'body' => 'An object remains at rest or in uniform motion unless acted on by a net external force. Net force is related to mass and acceleration by F = ma.',
            ],
            [
                'subject' => 'English Literature',
                'title' => 'Building a literary analysis',
                'body' => 'Support an interpretation with a clear claim, relevant textual evidence, and an explanation of how the evidence supports the claim.',
            ],
        ];

        foreach ($notes as $noteData) {
            $subject = $user->subjects()
                ->where('name', $noteData['subject'])
                ->firstOrFail();

            $subject->notes()->firstOrCreate(
                ['title' => $noteData['title']],
                [
                    'user_id' => $user->id,
                    'body' => $noteData['body'],
                ],
            );
        }
    }
}
