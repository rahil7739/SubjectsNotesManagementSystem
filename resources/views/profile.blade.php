<x-layout title="Profile settings">
    <section class="panel" aria-labelledby="profile-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">{{ $isAdminProfile ? 'Administration' : 'Study workspace' }}</p>
                <h1 id="profile-heading">Profile settings</h1>
                <p class="admin-page-intro">Update your account information and password.</p>
            </div>
        </div>

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

        <form method="post" action="{{ $isAdminProfile ? route('admin.profile.update') : route('profile.update') }}" class="stack-form">
            @csrf
            @method('put')
            <div class="input-row">
                <label>
                    <span>Name</span>
                    <input name="name" value="{{ old('name', $profileUser->name) }}" required maxlength="255" autocomplete="name" autofocus>
                </label>
                <label>
                    <span>Email</span>
                    <input name="email" type="email" value="{{ old('email', $profileUser->email) }}" required maxlength="255" autocomplete="email">
                </label>
                <label>
                    <span>New password</span>
                    <input name="password" type="password" minlength="8" autocomplete="new-password">
                </label>
                <label>
                    <span>Confirm new password</span>
                    <input name="password_confirmation" type="password" minlength="8" autocomplete="new-password">
                </label>
            </div>
            <p class="form-help">Leave both password fields empty to keep your current password.</p>
            <div class="form-actions">
                <button type="submit">Save profile</button>
                <a class="button-link" href="{{ $isAdminProfile ? route('admin.dashboard') : route('dashboard') }}">Back to dashboard</a>
            </div>
        </form>
    </section>
</x-layout>
