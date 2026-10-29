<x-layout title="Edit note">
    <section class="panel" aria-labelledby="edit-note-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Study workspace</p>
                <h1 id="edit-note-heading">Edit note</h1>
            </div>
            <a class="button-link" href="{{ route('notes.index') }}">Back to notes</a>
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

        <form method="post" action="{{ route('notes.update', $note) }}" enctype="multipart/form-data" class="stack-form">
            @csrf
            @method('put')
            <input type="hidden" name="notes_module" value="1">

            <div class="input-row">
                <label>
                    <span>Subject</span>
                    <select name="subject_id" required>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected((string) old('subject_id', $note->subject_id) === (string) $subject->id)>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Title</span>
                    <input name="title" value="{{ old('title', $note->title) }}" required maxlength="255" autofocus>
                </label>
            </div>

            <label>
                <span>Note</span>
                <textarea name="body" rows="8" maxlength="100000">{{ old('body', $note->body) }}</textarea>
            </label>

            @if ($note->attachments->isNotEmpty())
                <fieldset class="attachment-list">
                    <legend>Current attachments</legend>
                    <ul>
                        @foreach ($note->attachments as $attachment)
                            <li>
                                <a href="{{ route('attachments.download', $attachment) }}">{{ $attachment->original_name }}</a>
                                <span>({{ number_format($attachment->size / 1024, 1) }} KB)</span>
                            </li>
                        @endforeach
                    </ul>
                </fieldset>
            @endif

            <label>
                <span>Add attachments <small>(optional, up to 5 files; 10 MB each)</small></span>
                <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
            </label>
            <p class="form-help">Existing files are kept. Allowed formats: PDF, DOC, DOCX, TXT, JPG, JPEG, and PNG.</p>

            <div class="form-actions">
                <button type="submit">Save changes</button>
                <a class="button-link" href="{{ route('notes.index') }}">Cancel</a>
            </div>
        </form>
    </section>
</x-layout>
