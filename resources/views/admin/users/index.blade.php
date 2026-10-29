<x-layout title="User management">
    <section class="panel" aria-labelledby="users-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Administration</p>
                <h1 id="users-heading">User management</h1>
                <p class="admin-page-intro">View and manage regular user accounts and their study records.</p>
            </div>
            <a class="button-link button-link-primary" href="{{ route('admin.users.create') }}">Create user</a>
        </div>

        @if (session('status'))
            <p class="status-message" role="status">{{ session('status') }}</p>
        @endif

        <p class="admin-page-intro">Self-registered accounts appear here as pending until you approve them from the Actions column.</p>
        <div id="pending-approvals" class="subject-table-wrap">
            <table class="subject-table">
                <thead>
                    <tr>
                        <th scope="col">User</th>
                        <th scope="col">Email</th>
                        <th scope="col">Subjects</th>
                        <th scope="col">Notes</th>
                        <th scope="col">Registered</th>
                        <th scope="col">Status</th>
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
                            <td><time datetime="{{ $user->created_at->toIso8601String() }}">{{ $user->created_at->format('M j, Y') }}</time></td>
                            <td><span class="muted-pill">{{ $user->is_approved ? 'Active' : 'Pending approval' }}</span></td>
                            <td>
                                <div class="note-actions">
                                    @unless ($user->is_approved)
                                        <form method="post" action="{{ route('admin.users.approve', $user) }}">
                                            @csrf
                                            <button type="submit">Approve</button>
                                        </form>
                                    @endunless
                                    <a class="inline-link" href="{{ route('admin.users.show', $user) }}">Details</a>
                                    <a class="inline-link" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                                    <form method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Move this user and disable their access?')">
                                        @csrf
                                        @method('delete')
                                        <button class="button-ghost" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="subject-empty">No users found. Create a user to get started.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <nav class="pagination" aria-label="User list pages">
                {{ $users->links() }}
            </nav>
        @endif
    </section>
</x-layout>
