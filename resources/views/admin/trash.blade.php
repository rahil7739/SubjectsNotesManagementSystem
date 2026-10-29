<x-layout title="Deleted records">
<div class="admin-trash-page">
    <p><a href="{{ route('admin.dashboard') }}">Admin dashboard</a></p>
    <h1>Deleted records</h1>
    @if (session('status')) <p role="status">{{ session('status') }}</p> @endif
    <h2>Users</h2>
    @forelse ($users as $user)
        <article>
            <p>{{ $user->name }} — {{ $user->email }} — deleted {{ $user->deleted_at }}</p>
            <form method="post" action="{{ route('admin.users.restore', $user->id) }}">@csrf <button type="submit">Restore user</button></form>
            <form method="post" action="{{ route('admin.users.force-delete', $user->id) }}" onsubmit="return confirm('Permanently delete this user and all their subjects, notes, and attachments?')">
                @csrf @method('delete') <button type="submit">Permanently delete user</button>
            </form>
        </article>
    @empty
        <p>No deleted users.</p>
    @endforelse
    <h2>Subjects</h2>
    @forelse ($subjects as $subject)
        <article>
            <p>{{ $subject->name }} — {{ $subject->user->name }} — deleted {{ $subject->deleted_at }}</p>
            <form method="post" action="{{ route('admin.subjects.restore', $subject->id) }}">@csrf <button type="submit">Restore</button></form>
            <form method="post" action="{{ route('admin.subjects.force-delete', $subject->id) }}" onsubmit="return confirm('Permanently delete this subject and all its notes?')">
                @csrf @method('delete') <button type="submit">Permanently delete</button>
            </form>
        </article>
    @empty
        <p>No deleted subjects.</p>
    @endforelse
    <h2>Notes</h2>
    @forelse ($notes as $note)
        <article>
            <p>{{ $note->title }} — {{ $note->user->name }} — deleted {{ $note->deleted_at }}</p>
            <form method="post" action="{{ route('admin.notes.restore', $note->id) }}">@csrf <button type="submit">Restore</button></form>
            <form method="post" action="{{ route('admin.notes.force-delete', $note->id) }}" onsubmit="return confirm('Permanently delete this note and its attachments?')">
                @csrf @method('delete') <button type="submit">Permanently delete</button>
            </form>
        </article>
    @empty
        <p>No deleted notes.</p>
    @endforelse
</div>
</x-layout>
