<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\NoteAttachment;
use App\Models\Subject;
use App\Models\User;
use App\Services\AttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'data' => [
                'users' => User::where('role', RoleEnum::User)->count(),
                'deleted_users' => User::onlyTrashed()->where('role', RoleEnum::User)->count(),
                'subjects' => Subject::count(),
                'notes' => Note::count(),
                'deleted_subjects' => Subject::onlyTrashed()->count(),
                'deleted_notes' => Note::onlyTrashed()->count(),
            ],
        ]);
    }

    public function users(): JsonResponse
    {
        return response()->json(['data' => User::where('role', RoleEnum::User)
            ->withCount(['subjects', 'notes'])->orderBy('name')->get()]);
    }

    public function user(int $user): JsonResponse
    {
        $record = User::where('role', RoleEnum::User)->with([
            'subjects.notes.attachments',
            'notes.subject',
        ])->findOrFail($user);

        return response()->json(['data' => $record]);
    }

    public function trash(): JsonResponse
    {
        return response()->json([
            'users' => User::onlyTrashed()->where('role', RoleEnum::User)->latest('deleted_at')->get(),
            'subjects' => Subject::onlyTrashed()->with('user')->latest('deleted_at')->get(),
            'notes' => Note::onlyTrashed()->where('deleted_with_subject', false)
                ->with(['user', 'subject', 'attachments'])->latest('deleted_at')->get(),
        ]);
    }

    public function deleteUser(int $user): JsonResponse
    {
        $record = User::where('role', RoleEnum::User)->findOrFail($user);
        $record->apiTokens()->delete();
        $record->delete();

        return response()->json(['message' => 'User moved to deleted records.']);
    }

    public function restoreUser(int $user): JsonResponse
    {
        $record = User::onlyTrashed()->where('role', RoleEnum::User)->findOrFail($user);
        $record->restore();

        return response()->json(['data' => $record->fresh()]);
    }

    public function forceDeleteUser(int $user, AttachmentService $attachments): JsonResponse
    {
        $record = User::onlyTrashed()->where('role', RoleEnum::User)->findOrFail($user);
        $attachments->deleteForUser($record);
        $record->forceDelete();

        return response()->json(['message' => 'User and all their records were permanently deleted.']);
    }

    public function restoreSubject(int $subject): JsonResponse
    {
        $record = Subject::onlyTrashed()->findOrFail($subject);
        $record->restore();

        return response()->json(['data' => $record->fresh()]);
    }

    public function restoreNote(int $note): JsonResponse
    {
        $record = Note::onlyTrashed()->findOrFail($note);
        abort_unless(Subject::whereKey($record->subject_id)->exists(), 422, 'Restore the note subject first.');
        $record->restore();

        return response()->json(['data' => $record->fresh()]);
    }

    public function forceDeleteSubject(int $subject, AttachmentService $attachments): JsonResponse
    {
        $record = Subject::onlyTrashed()->with(['notes' => fn ($query) => $query->withTrashed()->with('attachments')])->findOrFail($subject);
        foreach ($record->notes as $note) {
            $attachments->deleteForNote($note);
        }
        $record->forceDelete();

        return response()->json(['message' => 'Subject permanently deleted.']);
    }

    public function forceDeleteNote(int $note, AttachmentService $attachments): JsonResponse
    {
        $record = Note::onlyTrashed()->with('attachments')->findOrFail($note);
        $attachments->deleteForNote($record);
        $record->forceDelete();

        return response()->json(['message' => 'Note permanently deleted.']);
    }

    public function deleteAttachment(NoteAttachment $attachment, AttachmentService $attachments): JsonResponse
    {
        $attachments->delete($attachment);

        return response()->json(['message' => 'Attachment deleted.']);
    }
}
