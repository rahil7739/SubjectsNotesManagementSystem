<x-layout title="All notes">
    <section class="panel" aria-labelledby="admin-notes-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Administration</p>
                <h1 id="admin-notes-heading">All notes</h1>
                <p class="admin-page-intro">Browse active notes and attachments across all user accounts.</p>
            </div>
            <a class="button-link button-link-primary" href="{{ route('admin.notes.create') }}">Add note</a>
        </div>
        @if (session('status'))
            <p class="status-message" role="status">{{ session('status') }}</p>
        @endif

        <div class="subject-table-wrap">
            <table class="subject-table">
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Owner</th>
                        <th scope="col">Attachments</th>
                        <th scope="col">Updated</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notes as $note)
                        <tr>
                            <th scope="row">
                                {{ $note->title }}
                                @if ($note->body)
                                    <p class="admin-note-preview">{{ \Illuminate\Support\Str::limit($note->body, 120) }}</p>
                                @endif
                            </th>
                            <td>{{ $note->subject->name }}</td>
                            <td>
                                @if ($note->user)
                                    <a href="{{ route('admin.users.show', $note->user) }}">{{ $note->user->name }}</a>
                                    <br><small>{{ $note->user->email }}</small>
                                @else
                                    Deleted user
                                @endif
                            </td>
                            <td>
                                @forelse ($note->attachments as $attachment)
                                    <a class="attachment-link" href="{{ route('admin.attachments.download', $attachment) }}">{{ $attachment->original_name }}</a>@if (!$loop->last)<br>@endif
                                @empty
                                    —
                                @endforelse
                            </td>
                            <td><time datetime="{{ $note->updated_at->toIso8601String() }}">{{ $note->updated_at->format('M j, Y') }}</time></td>
                            <td>
                                @if ($note->user)
                                    <a class="inline-link" href="{{ route('admin.users.show', $note->user) }}">Manage</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="subject-empty">No active notes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($notes->hasPages())
            <nav class="pagination" aria-label="Note list pages">{{ $notes->links() }}</nav>
        @endif
    </section>
</x-layout>
