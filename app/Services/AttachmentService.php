<?php

namespace App\Services;

use App\Models\Note;
use App\Models\NoteAttachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AttachmentService
{
    public function add(Note $note, UploadedFile $file): NoteAttachment
    {
        $path = $file->store('notes/'.$note->id, 'local');

        return $note->attachments()->create([
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    public function delete(NoteAttachment $attachment): void
    {
        Storage::disk('local')->delete($attachment->path);
        $attachment->delete();
    }

    public function deleteForNote(Note $note): void
    {
        foreach ($note->attachments as $attachment) {
            Storage::disk('local')->delete($attachment->path);
            $attachment->delete();
        }
    }

    public function deleteForUser(User $user): void
    {
        $notes = $user->notes()->withTrashed()->with('attachments')->get();

        foreach ($notes as $note) {
            $this->deleteForNote($note);
        }
    }
}
