<x-layout title="Your study dashboard">
    <header class="dashboard-header">
        <div>
            <p class="eyebrow">Your learning space</p>
            <h1>Welcome back, {{ $user->name }}</h1>
            <p>Keep your study materials organized and pick up where you left off.</p>
        </div>
        <div class="dashboard-quick-actions">
            <a class="button-link" href="{{ route('subjects.create') }}">Add subject</a>
            <a class="button-link button-link-primary" href="{{ route('notes.create') }}">Add note</a>
        </div>
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

    <section class="dashboard-stats" aria-label="Study overview">
        <article class="dashboard-stat-card">
            <span class="dashboard-stat-label">Subjects</span>
            <strong>{{ $subjects->count() }}</strong>
            <a href="{{ route('subject.index') }}">Manage subjects</a>
        </article>
        <article class="dashboard-stat-card">
            <span class="dashboard-stat-label">Notes</span>
            <strong>{{ $notes->count() }}</strong>
            <a href="{{ route('notes.index') }}">Browse notes</a>
        </article>
    </section>
</x-layout>
