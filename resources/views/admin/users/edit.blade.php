<x-layout title="Edit user">
    <section class="panel" aria-labelledby="edit-user-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Administration</p>
                <h1 id="edit-user-heading">Edit user</h1>
                <p class="admin-page-intro">Update {{ $managedUser->name }}’s account. Leave password fields empty to keep the current password.</p>
            </div>
            <a class="button-link" href="{{ route('admin.users.show', $managedUser) }}">Back to details</a>
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

        <form method="post" action="{{ route('admin.users.update', $managedUser) }}" class="stack-form">
            @csrf
            @method('put')
            <div class="input-row">
                <label>
                    <span>Name</span>
                    <input name="name" value="{{ old('name', $managedUser->name) }}" required maxlength="255" autocomplete="name" autofocus>
                </label>
                <label>
                    <span>Email</span>
                    <input name="email" type="email" value="{{ old('email', $managedUser->email) }}" required maxlength="255" autocomplete="email">
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
            <div class="form-actions">
                <button type="submit">Save changes</button>
                <a class="button-link" href="{{ route('admin.users.show', $managedUser) }}">Cancel</a>
            </div>
        </form>
    </section>
</x-layout>
