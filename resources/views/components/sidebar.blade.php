@php
    $isAdminWorkspace = request()->routeIs('admin.*') && auth('admin')->check();
    $links = $isAdminWorkspace
        ? [
            ['label' => 'Admin dashboard', 'url' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
            ['label' => 'Users', 'url' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.index', 'admin.users.create', 'admin.users.store', 'admin.users.show', 'admin.users.edit', 'admin.users.update')],
            ['label' => 'Deleted users', 'url' => route('admin.users.deleted'), 'active' => request()->routeIs('admin.users.deleted')],
            ['label' => 'Subjects', 'url' => route('admin.subjects.index'), 'active' => request()->routeIs('admin.subjects.index')],
            ['label' => 'Notes', 'url' => route('admin.notes.index'), 'active' => request()->routeIs('admin.notes.index')],
        ]
        : [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
            ['label' => 'My subjects', 'url' => route('subject.index'), 'active' => request()->routeIs('subject.index', 'subjects.create')],
            ['label' => 'My notes', 'url' => route('notes.index'), 'active' => request()->routeIs('notes.*')],
            ['label' => 'Profile settings', 'url' => route('profile.show'), 'active' => request()->routeIs('profile.show')],
        ];
@endphp

<aside class="workspace-sidebar" aria-label="{{ $isAdminWorkspace ? 'Admin navigation' : 'User navigation' }}">
    <div class="sidebar-heading">
        <span class="sidebar-heading-icon" aria-hidden="true">
            @if ($isAdminWorkspace)
                <svg viewBox="0 0 24 24" fill="none"><path d="M12 3 19 6v5c0 4.7-3 8.1-7 10-4-1.9-7-5.3-7-10V6l7-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @else
                <svg viewBox="0 0 24 24" fill="none"><path d="M4.5 5.5a2 2 0 0 1 2-2H19v16H6.5a2 2 0 0 1-2-2v-12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M4.5 16.5c0-1.1.9-2 2-2H19M9 7.5h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            @endif
        </span>
        <span class="sidebar-heading-copy">
            <strong>{{ $isAdminWorkspace ? 'Administration' : 'My workspace' }}</strong>
            <small>{{ $isAdminWorkspace ? 'Manage your platform' : 'Your learning space' }}</small>
        </span>
    </div>

    <nav class="sidebar-nav">
        <p class="sidebar-label">MENU</p>
        @foreach ($links as $link)
            <a
                href="{{ $link['url'] }}"
                class="sidebar-link{{ $link['active'] ? ' is-active' : '' }}"
                @if ($link['active']) aria-current="page" @endif
            >
                <span class="sidebar-link-icon" aria-hidden="true">
                    @if ($link['label'] === 'Dashboard' || $link['label'] === 'Admin dashboard')
                        <svg viewBox="0 0 24 24" fill="none"><rect x="4" y="4" width="6" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="14" y="4" width="6" height="5" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="14" y="13" width="6" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="4" y="15" width="6" height="5" rx="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
                    @elseif ($link['label'] === 'Users')
                        <svg viewBox="0 0 24 24" fill="none"><path d="M16 20v-1.5a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4V20M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM16 3.2a4 4 0 0 1 0 7.6M17 14.5a4 4 0 0 1 4 4V20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    @elseif ($link['label'] === 'Deleted users')
                        <svg viewBox="0 0 24 24" fill="none"><path d="M16 20v-1.5a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4V20M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM17 9h5M19.5 6.5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    @elseif ($link['label'] === 'Subjects')
                        <svg viewBox="0 0 24 24" fill="none"><path d="M5 4.5h14a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5H5A1.5 1.5 0 0 1 3.5 18V6A1.5 1.5 0 0 1 5 4.5Z" stroke="currentColor" stroke-width="1.7"/><path d="M8 9h8M8 13h8M8 17h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    @elseif ($link['label'] === 'Notes')
                        <svg viewBox="0 0 24 24" fill="none"><path d="M14 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8l-6-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 4v6h6M8 14h8M8 17h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @elseif ($link['label'] === 'My subjects' || $link['label'] === 'Subjects')
                        <svg viewBox="0 0 24 24" fill="none"><path d="M5 4.5h14a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5H5A1.5 1.5 0 0 1 3.5 18V6A1.5 1.5 0 0 1 5 4.5Z" stroke="currentColor" stroke-width="1.7"/><path d="M8 9h8M8 13h8M8 17h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    @elseif ($link['label'] === 'My notes' || $link['label'] === 'Notes')
                        <svg viewBox="0 0 24 24" fill="none"><path d="M14 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8l-6-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 4v6h6M8 14h8M8 17h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M5 20a7 7 0 0 1 14 0M19 4.5l1 1 2-2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @endif
                </span>
                <span>{{ $link['label'] }}</span>
                @if ($link['active']) <span class="sidebar-active-dot" aria-hidden="true"></span> @endif
            </a>
        @endforeach
    </nav>

    <div class="sidebar-bottom">
        <div class="sidebar-account">
            <span class="sidebar-avatar" aria-hidden="true">
                {{ mb_strtoupper(mb_substr($isAdminWorkspace ? auth('admin')->user()->name : auth('web')->user()->name, 0, 1)) }}
            </span>
            <span class="sidebar-account-copy">
                <strong>{{ $isAdminWorkspace ? auth('admin')->user()->name : auth('web')->user()->name }}</strong>
                <small>{{ $isAdminWorkspace ? 'Administrator' : 'Student account' }}</small>
            </span>
        </div>
    </div>
</aside>
