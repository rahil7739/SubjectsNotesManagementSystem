<x-layout title="Admin dashboard">
    <header class="dashboard-header">
        <div>
            <p class="eyebrow">Administration</p>
            <h1>Admin dashboard</h1>
            <p>Signed in as {{ auth('admin')->user()->name }} ({{ auth('admin')->user()->email }}).</p>
        </div>
        <a class="button-link button-link-primary" href="{{ route('admin.users.index') }}">Manage users</a>
    </header>

    @if (session('status'))
        <p class="status-message" role="status">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <div class="error-box" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="dashboard-stats admin-stats" aria-label="Platform statistics">
        <article class="dashboard-stat-card">
            <span class="dashboard-stat-label">Active users</span>
            <strong>{{ $stats['users'] }}</strong>
            <a href="{{ route('admin.users.index') }}">View all users</a>
        </article>
        <article class="dashboard-stat-card">
            <span class="dashboard-stat-label">Pending approvals</span>
            <strong>{{ $stats['pending_approvals'] }}</strong>
            <a href="{{ route('admin.users.index') }}#pending-approvals">Review requests</a>
        </article>
        <article class="dashboard-stat-card">
            <span class="dashboard-stat-label">Subjects</span>
            <strong>{{ $stats['subjects'] }}</strong>
            <span class="dashboard-stat-note">Across all users</span>
        </article>
        <article class="dashboard-stat-card">
            <span class="dashboard-stat-label">Notes</span>
            <strong>{{ $stats['notes'] }}</strong>
            <span class="dashboard-stat-note">Across all users</span>
        </article>
        <article class="dashboard-stat-card">
            <span class="dashboard-stat-label">Deleted users</span>
            <strong>{{ $stats['deleted_users'] }}</strong>
            <a href="{{ route('admin.trash') }}">Review deleted records</a>
        </article>
    </section>

    <section class="panel dashboard-section" aria-labelledby="users-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Account administration</p>
                <h2 id="users-heading">Users</h2>
            </div>
            <div class="dashboard-quick-actions">
                <a class="button-link" href="{{ route('admin.users.create') }}">Create user</a>
                <a class="inline-link" href="{{ route('admin.users.index') }}">Full user list</a>
            </div>
        </div>

        <div class="subject-table-wrap">
            <table class="subject-table">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Subjects</th>
                        <th scope="col">Notes</th>
                        <th scope="col">Joined</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users->take(8) as $user)
                        <tr>
                            <th scope="row">{{ $user->name }}</th>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->subjects_count }}</td>
                            <td>{{ $user->notes_count }}</td>
                            <td><time datetime="{{ $user->created_at->toIso8601String() }}">{{ $user->created_at->format('M j, Y') }}</time></td>
                            <td><a class="inline-link" href="{{ route('admin.users.show', $user) }}">View details</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="subject-empty">No active users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->count() > 8)
            <p class="dashboard-list-footer"><a href="{{ route('admin.users.index') }}">View all {{ $users->count() }} users</a></p>
        @endif
    </section>

    <section id="profile" class="panel dashboard-section" aria-labelledby="profile-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Account details</p>
                <h2 id="profile-heading">Profile settings</h2>
            </div>
        </div>
        <form method="post" action="{{ route('admin.profile.update') }}" class="stack-form">
            @csrf
            @method('put')
            <div class="input-row">
                <label>
                    <span>Name</span>
                    <input name="name" value="{{ old('name', auth('admin')->user()->name) }}" required maxlength="255" autocomplete="name">
                </label>
                <label>
                    <span>Email</span>
                    <input name="email" type="email" value="{{ old('email', auth('admin')->user()->email) }}" required autocomplete="email">
                </label>
                <label>
                    <span>New password</span>
                    <input name="password" type="password" autocomplete="new-password">
                </label>
                <label>
                    <span>Confirm new password</span>
                    <input name="password_confirmation" type="password" autocomplete="new-password">
                </label>
            </div>
            <div class="form-actions">
                <button type="submit">Save profile</button>
            </div>
        </form>
    </section>
</x-layout>
