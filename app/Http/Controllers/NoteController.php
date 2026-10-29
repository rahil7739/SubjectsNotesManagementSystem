<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Note;
use App\Models\NoteAttachment;
use App\Models\User;
use App\Notifications\StudyActivityNotification;
use App\Services\AttachmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NoteController extends Controller
{
    public function index(Request $request): View
    {
        $notes = $request->user('web')
            ->notes()
            ->with(['subject', 'attachments'])
            ->latest()
            ->get();

        return view('notes.list', ['notes' => $notes]);
    }

    public function create(Request $request): View
    {
        $subjects = $request->user('web')->subjects()->orderBy('name')->get();

        return view('notes.create', ['subjects' => $subjects]);
    }

    public function edit(Request $request, Note $note): View
    {
        $note = $request->user('web')->notes()
            ->with('attachments')
            ->findOrFail($note->id);
        $subjects = $request->user('web')->subjects()->orderBy('name')->get();

        return view('notes.edit', [
            'note' => $note,
            'subjects' => $subjects,
        ]);
    }

    public function store(Request $request, AttachmentService $attachments): RedirectResponse
    {
        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:100000'],
            'attachments' => ['sometimes', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,txt,jpg,jpeg,png'],
        ]);
        abort_unless($request->user('web')->subjects()->whereKey($data['subject_id'])->exists(), 404);

        $note = $request->user('web')->notes()->create(collect($data)->only(['subject_id', 'title', 'body'])->all());
        foreach ($request->file('attachments', []) as $file) {
            $attachments->add($note, $file);
        }
        User::where('role', RoleEnum::Admin)->get()->each(
            fn (User $admin) => $admin->notify(new StudyActivityNotification(
                'A user created a note',
                $request->user('web')->name.' added "'.$note->title.'" to '.$note->subject->name.'.',
                route('admin.notes.index'),
                'note',
            )),
        );

        $route = $request->boolean('notes_module') ? 'notes.index' : 'dashboard';

        return to_route($route)->with('status', 'Note created.');
    }

    public function update(Request $request, Note $note, AttachmentService $attachments): RedirectResponse
    {
        $note = $request->user('web')->notes()->findOrFail($note->id);
        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:100000'],
            'attachments' => ['sometimes', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,txt,jpg,jpeg,png'],
        ]);
        abort_unless($request->user('web')->subjects()->whereKey($data['subject_id'])->exists(), 404);
        $note->update(collect($data)->only(['subject_id', 'title', 'body'])->all());

        foreach ($request->file('attachments', []) as $file) {
            $attachments->add($note, $file);
        }

        $route = $request->boolean('notes_module') ? 'notes.index' : 'dashboard';

        return to_route($route)->with('status', 'Note updated.');
    }

    public function destroy(Request $request, Note $note): RedirectResponse
    {
        $request->user('web')->notes()->findOrFail($note->id)->delete();
        $route = $request->boolean('notes_module') ? 'notes.index' : 'dashboard';

        return to_route($route)->with('status', 'Note moved to deleted records.');
    }

    public function download(Request $request, NoteAttachment $attachment): StreamedResponse
    {
        abort_unless($request->user('web')->notes()->whereKey($attachment->note_id)->exists(), 404);

        return Storage::disk('local')->download($attachment->path, $attachment->original_name);
    }
}
