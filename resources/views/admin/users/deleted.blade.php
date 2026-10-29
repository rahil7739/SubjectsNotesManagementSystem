<x-layout title="Deleted users">
    <section class="panel" aria-labelledby="deleted-users-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Administration</p>
                <h1 id="deleted-users-heading">Deleted users</h1>
                <p class="admin-page-intro">Restore accounts or permanently delete them and their study records.</p>
            </div>
            <a class="button-link" href="{{ route('admin.users.index') }}">Active users</a>
        </div>

        @if (session('status'))
            <p class="status-message" role="status">{{ session('status') }}</p>
        @endif

        <div class="subject-table-wrap">
            <table class="subject-table">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Subjects</th>
                        <th scope="col">Notes</th>
                        <th scope="col">Deleted</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <th scope="row">{{ $user->name }}</th>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->subjects_count }}</td>
                            <td>{{ $user->notes_count }}</td>
                            <td><time datetime="{{ $user->deleted_at->toIso8601String() }}">{{ $user->deleted_at->format('M j, Y g:i A') }}</time></td>
                            <td>
                                <div class="note-actions">
                                    <form method="post" action="{{ route('admin.users.restore', $user->id) }}">
                                        @csrf
                                        <button type="submit">Restore</button>
                                    </form>
                                    <form method="post" action="{{ route('admin.users.force-delete', $user->id) }}" onsubmit="return confirm('Permanently delete this user and all their subjects, notes, and attachments? This cannot be undone.')">
                                        @csrf
                                        @method('delete')
                                        <button class="button-danger" type="submit">Permanently delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="subject-empty">There are no deleted users.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <nav class="pagination" aria-label="Deleted user list pages">{{ $users->links() }}</nav>
        @endif
    </section>
</x-layout>
