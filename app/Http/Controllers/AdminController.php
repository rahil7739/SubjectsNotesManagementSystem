<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Note;
use App\Models\NoteAttachment;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\StudyActivityNotification;
use App\Notifications\UserAccountNotification;
use App\Services\AttachmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::where('role', RoleEnum::User)->where('is_approved', true)->count(),
                'pending_approvals' => User::where('role', RoleEnum::User)->where('is_approved', false)->count(),
                'deleted_users' => User::onlyTrashed()->where('role', RoleEnum::User)->count(),
                'subjects' => Subject::count(),
                'notes' => Note::count(),
                'deleted' => Subject::onlyTrashed()->count() + Note::onlyTrashed()->count(),
            ],
            'users' => User::where('role', RoleEnum::User)->where('is_approved', true)
                ->withCount(['subjects', 'notes'])->orderBy('name')->get(),
        ]);
    }

    public function users(): View
    {
        return view('admin.users.index', [
            'users' => User::where('role', RoleEnum::User)
                ->withCount(['subjects', 'notes'])
                ->orderBy('is_approved')
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function subjects(): View
    {
        return view('admin.subjects.index', [
            'subjects' => Subject::with('user')
                ->withCount('notes')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function createSubject(): View
    {
        return view('admin.subjects.create', [
            'users' => User::where('role', RoleEnum::User)->orderBy('name')->get(),
        ]);
    }

    public function assignSubject(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
        ]);
        $user = User::where('role', RoleEnum::User)->findOrFail($data['user_id']);
        $subject = $user->subjects()->create(collect($data)->only(['name', 'description'])->all());
        $user->notify(new StudyActivityNotification(
            'A subject was added to your workspace',
            'Your administrator added "'.$subject->name.'" to your subjects.',
            route('subject.index'),
            'subject',
        ));

        return to_route('admin.subjects.index')->with('status', 'Subject created and assigned to '.$user->name.'.');
    }

    public function notes(): View
    {
        return view('admin.notes.index', [
            'notes' => Note::with(['user', 'subject', 'attachments'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function createNote(): View
    {
        return view('admin.notes.create', [
            'users' => User::where('role', RoleEnum::User)
                ->with(['subjects' => fn ($query) => $query->orderBy('name')])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function assignNote(Request $request, AttachmentService $attachments): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:100000'],
            'attachments' => ['sometimes', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,txt,jpg,jpeg,png'],
        ]);
        $user = User::where('role', RoleEnum::User)->findOrFail($data['user_id']);
        abort_unless($user->subjects()->whereKey($data['subject_id'])->exists(), 422, 'Choose a subject belonging to the selected user.');

        $note = $user->notes()->create(collect($data)->only(['subject_id', 'title', 'body'])->all());
        foreach ($request->file('attachments', []) as $file) {
            $attachments->add($note, $file);
        }
        $user->notify(new StudyActivityNotification(
            'A new note was added',
            'Your administrator added "'.$note->title.'" to '.$note->subject->name.'.',
            route('notes.index'),
            'note',
        ));

        return to_route('admin.notes.index')->with('status', 'Note created and assigned to '.$user->name.'.');
    }

    public function createUser(): View
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->role = RoleEnum::User;
        $user->is_approved = true;
        $user->save();
        $user->notify(new UserAccountNotification(
            'Your StudySpace account is ready',
            'An administrator created an account for you. Please contact your administrator for your initial password.',
        ));

        return to_route('admin.users.show', $user)->with('status', 'User created.');
    }

    public function approveUser(User $user): RedirectResponse
    {
        abort_unless($user->role === RoleEnum::User, 404);

        if (! $user->is_approved) {
            $user->is_approved = true;
            $user->save();
            $user->notify(new UserAccountNotification(
                'Your StudySpace account has been approved',
                'Your account is approved and ready to use.',
            ));
        }

        return to_route('admin.users.index')->with('status', 'User approved and notified by email.');
    }

    public function editUser(User $user): View
    {
        abort_unless($user->role === RoleEnum::User, 404);

        return view('admin.users.edit', ['managedUser' => $user]);
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === RoleEnum::User, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (filled($data['password'] ?? null)) {
            $user->password = $data['password'];
        }
        $user->save();

        return to_route('admin.users.show', $user)->with('status', 'User updated.');
    }

    public function showUser(User $user): View
    {
        abort_unless($user->role === RoleEnum::User, 404);

        return view('admin.user', [
            'managedUser' => $user,
            'subjects' => $user->subjects()->with('notes.attachments')->withCount('notes')->latest()->get(),
            'notes' => $user->notes()->with(['subject', 'attachments'])->latest()->get(),
        ]);
    }

    public function trash(): View
    {
        return view('admin.trash', [
            'users' => User::onlyTrashed()->where('role', RoleEnum::User)->latest('deleted_at')->get(),
            'subjects' => Subject::onlyTrashed()->with('user')->latest('deleted_at')->get(),
            'notes' => Note::onlyTrashed()->where('deleted_with_subject', false)
                ->with(['user', 'subject', 'attachments'])->latest('deleted_at')->get(),
        ]);
    }

    public function deletedUsers(): View
    {
        return view('admin.users.deleted', [
            'users' => User::onlyTrashed()
                ->where('role', RoleEnum::User)
                ->withCount(['subjects', 'notes'])
                ->latest('deleted_at')
                ->paginate(15),
        ]);
    }

    public function deleteUser(User $user): RedirectResponse
    {
        abort_unless($user->role === RoleEnum::User, 404);
        $user->apiTokens()->delete();
        $user->delete();

        return to_route('admin.users.deleted')->with('status', 'User moved to deleted users.');
    }

    public function restoreUser(int $user): RedirectResponse
    {
        $record = User::onlyTrashed()->where('role', RoleEnum::User)->findOrFail($user);
        $record->restore();

        return back()->with('status', 'User restored.');
    }

    public function forceDeleteUser(int $user, AttachmentService $attachments): RedirectResponse
    {
        $record = User::onlyTrashed()->where('role', RoleEnum::User)->findOrFail($user);
        $attachments->deleteForUser($record);
        $record->forceDelete();

        return back()->with('status', 'User and all their records were permanently deleted.');
    }

    public function storeSubject(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === RoleEnum::User, 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
        ]);
        $subject = $user->subjects()->create($data);
        $user->notify(new StudyActivityNotification(
            'A subject was added to your workspace',
            'Your administrator added "'.$subject->name.'" to your subjects.',
            route('subject.index'),
            'subject',
        ));

        return back()->with('status', 'Subject created.');
    }

    public function updateSubject(Request $request, int $subject): RedirectResponse
    {
        $record = Subject::findOrFail($subject);
        $record->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
        ]));

        return to_route('admin.users.show', $record->user_id)->with('status', 'Subject updated.');
    }

    public function deleteSubject(int $subject): RedirectResponse
    {
        $record = Subject::findOrFail($subject);
        $userId = $record->user_id;
        $record->delete();

        return to_route('admin.users.show', $userId)->with('status', 'Subject moved to deleted records.');
    }

    public function storeNote(Request $request, User $user, AttachmentService $attachments): RedirectResponse
    {
        abort_unless($user->role === RoleEnum::User, 404);
        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:100000'],
            'attachments' => ['sometimes', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,txt,jpg,jpeg,png'],
        ]);
        abort_unless($user->subjects()->whereKey($data['subject_id'])->exists(), 422);

        $note = $user->notes()->create(collect($data)->only(['subject_id', 'title', 'body'])->all());
        foreach ($request->file('attachments', []) as $file) {
            $attachments->add($note, $file);
        }
        $user->notify(new StudyActivityNotification(
            'A new note was added',
            'Your administrator added "'.$note->title.'" to '.$note->subject->name.'.',
            route('notes.index'),
            'note',
        ));

        return back()->with('status', 'Note created.');
    }

    public function updateNote(Request $request, int $note, AttachmentService $attachments): RedirectResponse
    {
        $record = Note::findOrFail($note);
        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:100000'],
            'attachments' => ['sometimes', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,txt,jpg,jpeg,png'],
        ]);
        abort_unless($record->user->subjects()->whereKey($data['subject_id'])->exists(), 422);
        $record->update(collect($data)->only(['subject_id', 'title', 'body'])->all());
        foreach ($request->file('attachments', []) as $file) {
            $attachments->add($record, $file);
        }
        $record->user->notify(new StudyActivityNotification(
            'A note was updated',
            'Your administrator updated "'.$record->title.'".',
            route('notes.index'),
            'note',
        ));

        return to_route('admin.users.show', $record->user_id)->with('status', 'Note updated.');
    }

    public function deleteNote(int $note): RedirectResponse
    {
        $record = Note::findOrFail($note);
        $userId = $record->user_id;
        $record->delete();

        return to_route('admin.users.show', $userId)->with('status', 'Note moved to deleted records.');
    }

    public function restoreSubject(int $subject): RedirectResponse
    {
        Subject::onlyTrashed()->findOrFail($subject)->restore();

        return back()->with('status', 'Subject restored.');
    }

    public function restoreNote(int $note): RedirectResponse
    {
        $record = Note::onlyTrashed()->findOrFail($note);
        abort_unless(Subject::whereKey($record->subject_id)->exists(), 422, 'Restore the note subject first.');
        $record->restore();

        return back()->with('status', 'Note restored.');
    }

    public function forceDeleteSubject(int $subject, AttachmentService $attachments): RedirectResponse
    {
        $record = Subject::onlyTrashed()->with(['notes' => fn ($query) => $query->withTrashed()->with('attachments')])->findOrFail($subject);
        foreach ($record->notes as $note) {
            $attachments->deleteForNote($note);
        }
        $record->forceDelete();

        return back()->with('status', 'Subject permanently deleted.');
    }

    public function forceDeleteNote(int $note, AttachmentService $attachments): RedirectResponse
    {
        $record = Note::onlyTrashed()->with('attachments')->findOrFail($note);
        $attachments->deleteForNote($record);
        $record->forceDelete();

        return back()->with('status', 'Note permanently deleted.');
    }

    public function downloadAttachment(NoteAttachment $attachment): StreamedResponse
    {
        return Storage::disk('local')->download($attachment->path, $attachment->original_name);
    }
}
