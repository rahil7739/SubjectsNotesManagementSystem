<x-layout title="Add subject">
    <section class="panel" aria-labelledby="admin-create-subject-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Administration</p>
                <h1 id="admin-create-subject-heading">Add subject</h1>
                <p class="admin-page-intro">Create a subject and assign it to a user account.</p>
            </div>
            <a class="button-link" href="{{ route('admin.subjects.index') }}">Back to subjects</a>
        </div>

        @if ($errors->any())
            <div class="error-box" role="alert">
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        @if ($users->isEmpty())
            <div class="empty-state">
                <h2>No active users</h2>
                <p>Create a user account before assigning a subject.</p>
                <a class="button-link button-link-primary" href="{{ route('admin.users.create') }}">Create user</a>
            </div>
        @else
            <form method="post" action="{{ route('admin.subjects.assign') }}" class="stack-form">
                @csrf
                <div class="input-row">
                    <label>
                        <span>Assign to user</span>
                        <select name="user_id" required>
                            <option value="">Choose a user</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>{{ $user->name }} — {{ $user->email }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Subject name</span>
                        <input name="name" value="{{ old('name') }}" required maxlength="255" autofocus>
                    </label>
                </div>
                <label>
                    <span>Description <small>(optional)</small></span>
                    <textarea name="description" rows="4" maxlength="10000">{{ old('description') }}</textarea>
                </label>
                <div class="form-actions">
                    <button type="submit">Create and assign subject</button>
                    <a class="button-link" href="{{ route('admin.subjects.index') }}">Cancel</a>
                </div>
            </form>
        @endif
    </section>
</x-layout>
