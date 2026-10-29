<x-layout :title="$managedUser->name.' — User details'">
    <header class="dashboard-header">
        <div>
            <p class="eyebrow">User management</p>
            <h1>{{ $managedUser->name }}</h1>
            <p>{{ $managedUser->email }}</p>
        </div>
        <div class="dashboard-quick-actions">
            <a class="button-link" href="{{ route('admin.users.index') }}">Back to users</a>
            <a class="button-link button-link-primary" href="{{ route('admin.users.edit', $managedUser) }}">Edit user</a>
            <form method="post" action="{{ route('admin.users.destroy', $managedUser) }}" onsubmit="return confirm('Move this user and disable their access?')">
                @csrf
                @method('delete')
                <button class="button-ghost" type="submit">Delete user</button>
            </form>
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

    <section class="panel dashboard-section" aria-labelledby="account-details-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Account overview</p>
                <h2 id="account-details-heading">User details</h2>
            </div>
        </div>
        <dl class="user-details-grid">
            <div><dt>User ID</dt><dd>{{ $managedUser->id }}</dd></div>
            <div><dt>Name</dt><dd>{{ $managedUser->name }}</dd></div>
            <div><dt>Email</dt><dd>{{ $managedUser->email }}</dd></div>
            <div><dt>Role</dt><dd>{{ $managedUser->role->value }}</dd></div>
            <div><dt>Registered</dt><dd><time datetime="{{ $managedUser->created_at->toIso8601String() }}">{{ $managedUser->created_at->format('M j, Y g:i A') }}</time></dd></div>
            <div><dt>Email verified</dt><dd>{{ $managedUser->email_verified_at?->format('M j, Y g:i A') ?? 'Not verified' }}</dd></div>
            <div><dt>Subjects</dt><dd>{{ $subjects->count() }}</dd></div>
            <div><dt>Notes</dt><dd>{{ $notes->count() }}</dd></div>
        </dl>
    </section>

    <section class="panel dashboard-section" aria-labelledby="user-subjects-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Study records</p>
                <h2 id="user-subjects-heading">Subjects</h2>
            </div>
        </div>

        <form method="post" action="{{ route('admin.subjects.store', $managedUser) }}" class="stack-form">
            @csrf
            <div class="input-row">
                <label>
                    <span>New subject name</span>
                    <input name="name" value="{{ old('name') }}" required maxlength="255">
                </label>
                <label>
                    <span>Description <small>(optional)</small></span>
                    <textarea name="description" rows="2" maxlength="10000">{{ old('description') }}</textarea>
                </label>
            </div>
            <div class="form-actions"><button type="submit">Add subject</button></div>
        </form>

        @forelse ($subjects as $subject)
            <article class="admin-record-card">
                <div class="section-header compact">
                    <div>
                        <h3>{{ $subject->name }}</h3>
                        <p>{{ $subject->description ?: 'No description.' }}</p>
                        <span class="muted-pill">{{ $subject->notes_count }} note{{ $subject->notes_count === 1 ? '' : 's' }}</span>
                    </div>
                    <form method="post" action="{{ route('admin.subjects.destroy', $subject) }}" onsubmit="return confirm('Move this subject and its notes to deleted records?')">
                        @csrf
                        @method('delete')
                        <button class="button-ghost" type="submit">Delete subject</button>
                    </form>
                </div>
                <form method="post" action="{{ route('admin.subjects.update', $subject) }}" class="stack-form">
                    @csrf
                    @method('put')
                    <div class="input-row">
                        <label>
                            <span>Subject name</span>
                            <input name="name" value="{{ $subject->name }}" required maxlength="255">
                        </label>
                        <label>
                            <span>Description</span>
                            <textarea name="description" rows="2" maxlength="10000">{{ $subject->description }}</textarea>
                        </label>
                    </div>
                    <div class="form-actions justify-end"><button type="submit">Save subject</button></div>
                </form>
            </article>
        @empty
            <div class="empty-state">
                <h3>No active subjects</h3>
                <p>Add a subject before creating a note for this user.</p>
            </div>
        @endforelse
    </section>

    <section class="panel dashboard-section" aria-labelledby="user-notes-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Study records</p>
                <h2 id="user-notes-heading">Notes</h2>
            </div>
        </div>

        @if ($subjects->isNotEmpty())
            <form method="post" action="{{ route('admin.notes.store', $managedUser) }}" enctype="multipart/form-data" class="stack-form">
                @csrf
                <div class="input-row">
                    <label>
                        <span>Subject</span>
                        <select name="subject_id" required>
                            <option value="">Choose a subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected((string) old('subject_id') === (string) $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Title</span>
                        <input name="title" value="{{ old('title') }}" required maxlength="255">
                    </label>
                </div>
                <label>
                    <span>Note</span>
                    <textarea name="body" rows="4" maxlength="100000">{{ old('body') }}</textarea>
                </label>
                <label>
                    <span>Attachments <small>(optional, up to 5 files; 10 MB each)</small></span>
                    <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                </label>
                <div class="form-actions"><button type="submit">Add note</button></div>
            </form>
        @else
            <p class="form-help">Add an active subject before adding notes.</p>
        @endif

        @forelse ($notes as $note)
            <article class="admin-record-card">
                <div class="section-header compact">
                    <div>
                        <h3>{{ $note->title }}</h3>
                        <p>{{ $note->subject->name }} · updated {{ $note->updated_at->format('M j, Y') }}</p>
                    </div>
                    <form method="post" action="{{ route('admin.notes.destroy', $note) }}" onsubmit="return confirm('Move this note to deleted records?')">
                        @csrf
                        @method('delete')
                        <button class="button-ghost" type="submit">Delete note</button>
                    </form>
                </div>
                @if ($note->body)
                    <p class="admin-note-body">{{ $note->body }}</p>
                @endif
                @if ($note->attachments->isNotEmpty())
                    <ul class="admin-attachment-list">
                        @foreach ($note->attachments as $attachment)
                            <li><a href="{{ route('admin.attachments.download', $attachment) }}">{{ $attachment->original_name }}</a> <small>({{ number_format($attachment->size / 1024, 1) }} KB)</small></li>
                        @endforeach
                    </ul>
                @endif
                <form method="post" action="{{ route('admin.notes.update', $note) }}" enctype="multipart/form-data" class="stack-form">
                    @csrf
                    @method('put')
                    <div class="input-row">
                        <label>
                            <span>Subject</span>
                            <select name="subject_id" required>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}" @selected($subject->id === $note->subject_id)>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Title</span>
                            <input name="title" value="{{ $note->title }}" required maxlength="255">
                        </label>
                    </div>
                    <label>
                        <span>Note</span>
                        <textarea name="body" rows="4" maxlength="100000">{{ $note->body }}</textarea>
                    </label>
                    <label>
                        <span>Add attachments</span>
                        <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                    </label>
                    <div class="form-actions justify-end"><button type="submit">Save note</button></div>
                </form>
            </article>
        @empty
            <div class="empty-state">
                <h3>No active notes</h3>
                <p>This user has not added any notes yet.</p>
            </div>
        @endforelse
    </section>
</x-layout>
