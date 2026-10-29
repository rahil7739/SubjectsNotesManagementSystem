<x-layout title="Create user">
    <section class="panel" aria-labelledby="create-user-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Administration</p>
                <h1 id="create-user-heading">Create user</h1>
                <p class="admin-page-intro">Create a regular user account. Administrator privileges are not assigned here.</p>
            </div>
            <a class="button-link" href="{{ route('admin.users.index') }}">Back to users</a>
        </div>

        @if ($errors->any())
            <div class="error-box" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('admin.users.store') }}" class="stack-form">
            @csrf
            <div class="input-row">
                <label>
                    <span>Name</span>
                    <input name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name" autofocus>
                </label>
                <label>
                    <span>Email</span>
                    <input name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email">
                </label>
                <label>
                    <span>Password</span>
                    <input name="password" type="password" required minlength="8" autocomplete="new-password">
                </label>
                <label>
                    <span>Confirm password</span>
                    <input name="password_confirmation" type="password" required minlength="8" autocomplete="new-password">
                </label>
            </div>
            <div class="form-actions">
                <button type="submit">Create user</button>
                <a class="button-link" href="{{ route('admin.users.index') }}">Cancel</a>
            </div>
        </form>
    </section>
</x-layout>
