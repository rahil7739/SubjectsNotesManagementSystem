<x-layout title="All subjects">
    <section class="panel" aria-labelledby="admin-subjects-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Administration</p>
                <h1 id="admin-subjects-heading">All subjects</h1>
                <p class="admin-page-intro">Browse active subjects across all user accounts.</p>
            </div>
            <a class="button-link button-link-primary" href="{{ route('admin.subjects.create') }}">Add subject</a>
        </div>
        @if (session('status'))
            <p class="status-message" role="status">{{ session('status') }}</p>
        @endif

        <div class="subject-table-wrap">
            <table class="subject-table">
                <thead>
                    <tr>
                        <th scope="col">Subject</th>
                        <th scope="col">Description</th>
                        <th scope="col">Owner</th>
                        <th scope="col">Notes</th>
                        <th scope="col">Created</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subjects as $subject)
                        <tr>
                            <th scope="row">{{ $subject->name }}</th>
                            <td>{{ \Illuminate\Support\Str::limit($subject->description ?: '—', 100) }}</td>
                            <td>
                                @if ($subject->user)
                                    <a href="{{ route('admin.users.show', $subject->user) }}">{{ $subject->user->name }}</a>
                                    <br><small>{{ $subject->user->email }}</small>
                                @else
                                    Deleted user
                                @endif
                            </td>
                            <td>{{ $subject->notes_count }}</td>
                            <td><time datetime="{{ $subject->created_at->toIso8601String() }}">{{ $subject->created_at->format('M j, Y') }}</time></td>
                            <td>
                                @if ($subject->user)
                                    <a class="inline-link" href="{{ route('admin.users.show', $subject->user) }}">Manage</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="subject-empty">No active subjects found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($subjects->hasPages())
            <nav class="pagination" aria-label="Subject list pages">{{ $subjects->links() }}</nav>
        @endif
    </section>
</x-layout>
