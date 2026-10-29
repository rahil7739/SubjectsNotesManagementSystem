<x-layout title="Add note">
    <section class="panel" aria-labelledby="create-note-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Study workspace</p>
                <h1 id="create-note-heading">Add a note</h1>
            </div>
            <a class="button-link" href="{{ route('notes.index') }}">Back to notes</a>
        </div>

        @if ($subjects->isEmpty())
            <div class="empty-state">
                <h2>Create a subject first</h2>
                <p>Every note belongs to a subject. Add a subject before creating your note.</p>
                <a class="button-link button-link-primary" href="{{ route('subjects.create') }}">Add subject</a>
            </div>
        @else
            @if ($errors->any())
                <div class="error-box" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" action="{{ route('notes.store') }}" enctype="multipart/form-data" class="stack-form">
                @csrf
                <input type="hidden" name="notes_module" value="1">
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
                        <input name="title" value="{{ old('title') }}" required maxlength="255" autofocus>
                    </label>
                </div>

                <label>
                    <span>Note</span>
                    <textarea name="body" rows="8" maxlength="100000">{{ old('body') }}</textarea>
                </label>

                <label>
                    <span>Attachments <small>(optional, up to 5 files; 10 MB each)</small></span>
                    <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                </label>
                <p class="form-help">Allowed formats: PDF, DOC, DOCX, TXT, JPG, JPEG, and PNG.</p>

                <div class="form-actions">
                    <button type="submit">Save note</button>
                    <a class="button-link" href="{{ route('notes.index') }}">Cancel</a>
                </div>
            </form>
        @endif
    </section>
</x-layout>
