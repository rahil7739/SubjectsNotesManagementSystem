<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\NoteAttachment;
use App\Models\Subject;
use App\Services\AttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NoteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Note::with(['subject', 'attachments', 'user'])->latest();
        if ($request->user()->role !== RoleEnum::Admin) {
            $query->where('user_id', $request->user()->id);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(Request $request, AttachmentService $attachments): JsonResponse
    {
        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:100000'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'attachments' => ['sometimes', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,txt,jpg,jpeg,png'],
        ]);
        $ownerId = $request->user()->role === RoleEnum::Admin
            ? ($data['user_id'] ?? Subject::findOrFail($data['subject_id'])->user_id)
            : $request->user()->id;
        abort_unless(Subject::where('user_id', $ownerId)->whereKey($data['subject_id'])->exists(), 422);

        $note = Note::create(collect($data)->only(['subject_id', 'title', 'body'])->merge(['user_id' => $ownerId])->all());
        foreach ($request->file('attachments', []) as $file) {
            $attachments->add($note, $file);
        }

        return response()->json(['data' => $note->load('attachments', 'subject')], 201);
    }

    public function show(Request $request, int $note): JsonResponse
    {
        return response()->json(['data' => $this->findAccessible($request, $note)->load('attachments', 'subject', 'user')]);
    }

    public function update(Request $request, int $note, AttachmentService $attachments): JsonResponse
    {
        $record = $this->findAccessible($request, $note);
        $data = $request->validate([
            'subject_id' => ['sometimes', 'required', 'integer', 'exists:subjects,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:100000'],
            'attachments' => ['sometimes', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,txt,jpg,jpeg,png'],
        ]);
        $subjectId = $data['subject_id'] ?? $record->subject_id;
        abort_unless(Subject::where('user_id', $record->user_id)->whereKey($subjectId)->exists(), 422);
        $record->update(collect($data)->only(['subject_id', 'title', 'body'])->all());
        foreach ($request->file('attachments', []) as $file) {
            $attachments->add($record, $file);
        }

        return response()->json(['data' => $record->load('attachments', 'subject')]);
    }

    public function destroy(Request $request, int $note): JsonResponse
    {
        // dd($note);
        $this->findAccessible($request, $note)->delete();

        return response()->json(['message' => 'Note moved to deleted records.']);
    }

    public function download(Request $request, NoteAttachment $attachment): StreamedResponse
    {
        $note = $attachment->note;
        abort_unless($note !== null && ($request->user()->role === RoleEnum::Admin || $note->user_id === $request->user()->id), 404);

        return Storage::disk('local')->download($attachment->path, $attachment->original_name);
    }

    private function findAccessible(Request $request, int $id): Note
    {
        $query = Note::query();
        if ($request->user()->role !== RoleEnum::Admin) {
            $query->where('user_id', $request->user()->id);
        }

        return $query->findOrFail($id);
    }
}
