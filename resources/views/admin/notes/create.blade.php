<x-layout title="Add note">
    <section class="panel" aria-labelledby="admin-create-note-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Administration</p>
                <h1 id="admin-create-note-heading">Add note</h1>
                <p class="admin-page-intro">Create a note for a user and one of their subjects.</p>
            </div>
            <a class="button-link" href="{{ route('admin.notes.index') }}">Back to notes</a>
        </div>

        @if ($errors->any())
            <div class="error-box" role="alert">
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        @if ($users->isEmpty())
            <div class="empty-state">
                <h2>No active users</h2>
                <p>Create a user account and subject before assigning a note.</p>
                <a class="button-link button-link-primary" href="{{ route('admin.users.create') }}">Create user</a>
            </div>
        @elseif ($users->every(fn ($user) => $user->subjects->isEmpty()))
            <div class="empty-state">
                <h2>No subjects available</h2>
                <p>Create and assign a subject before adding a note.</p>
                <a class="button-link button-link-primary" href="{{ route('admin.subjects.create') }}">Add subject</a>
            </div>
        @else
            <form method="post" action="{{ route('admin.notes.assign') }}" enctype="multipart/form-data" class="stack-form">
                @csrf
                <div class="input-row">
                    <label>
                        <span>Assign to user</span>
                        <select id="note-user-id" name="user_id" required>
                            <option value="">Choose a user</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>{{ $user->name }} — {{ $user->email }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Subject</span>
                        <select id="note-subject-id" name="subject_id" required>
                            <option value="">Choose a subject</option>
                            @foreach ($users as $user)
                                @foreach ($user->subjects as $subject)
                                    <option value="{{ $subject->id }}" data-user-id="{{ $user->id }}" @selected((string) old('subject_id') === (string) $subject->id)>{{ $subject->name }} — {{ $user->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </label>
                </div>
                <label>
                    <span>Title</span>
                    <input name="title" value="{{ old('title') }}" required maxlength="255" autofocus>
                </label>
                <label>
                    <span>Note</span>
                    <textarea name="body" rows="7" maxlength="100000">{{ old('body') }}</textarea>
                </label>
                <label>
                    <span>Attachments <small>(optional, up to 5 files; 10 MB each)</small></span>
                    <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                </label>
                <p class="form-help">Allowed formats: PDF, DOC, DOCX, TXT, JPG, JPEG, and PNG.</p>
                <div class="form-actions">
                    <button type="submit">Create and assign note</button>
                    <a class="button-link" href="{{ route('admin.notes.index') }}">Cancel</a>
                </div>
            </form>
            <script>
                (() => {
                    const userSelect = document.getElementById('note-user-id');
                    const subjectSelect = document.getElementById('note-subject-id');
                    const filterSubjects = () => {
                        const userId = userSelect.value;
                        for (const option of subjectSelect.options) {
                            if (!option.dataset.userId) continue;
                            option.hidden = userId !== '' && option.dataset.userId !== userId;
                            option.disabled = option.hidden;
                        }
                        if (subjectSelect.selectedOptions[0]?.disabled) subjectSelect.value = '';
                    };
                    userSelect.addEventListener('change', filterSubjects);
                    filterSubjects();
                })();
            </script>
        @endif
    </section>
</x-layout>
