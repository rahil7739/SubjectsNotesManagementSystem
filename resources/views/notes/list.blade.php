<x-layout title="My notes">
    <section class="panel" aria-labelledby="notes-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Study workspace</p>
                <h1 id="notes-heading">My notes</h1>
            </div>
            <a class="button-link button-link-primary" href="{{ route('notes.create') }}">Add note</a>
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
                        <th scope="col">Note</th>
                        <th scope="col">Attachments</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notes as $note)
                        <tr>
                            <th scope="row">{{ $note->title }}</th>
                            <td>{{ $note->subject->name }}</td>
                            <td class="note-preview">{{ \Illuminate\Support\Str::limit($note->body ?: '—', 120) }}</td>
                            <td>
                                @forelse ($note->attachments as $attachment)
                                    <a class="attachment-link" href="{{ route('attachments.download', $attachment) }}">{{ $attachment->original_name }}</a>@if (!$loop->last)<br>@endif
                                @empty
                                    —
                                @endforelse
                            </td>
                            <td>
                                <div class="note-actions">
                                    <a class="inline-link" href="{{ route('notes.edit', $note) }}">Edit</a>
                                    <form method="post" action="{{ route('notes.destroy', $note) }}" onsubmit="return confirm('Move this note to deleted records?')">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="notes_module" value="1">
                                        <button class="button-ghost" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="subject-empty">No notes yet. Select “Add note” to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layout>
