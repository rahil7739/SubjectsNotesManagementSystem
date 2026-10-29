<x-layout title="Add subject">
    <section class="panel" aria-labelledby="create-subject-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Study workspace</p>
                <h1 id="create-subject-heading">Add a subject</h1>
            </div>
            <a class="button-link" href="{{ route('subject.index') }}">Back to subjects</a>
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

        <form method="post" action="{{ route('subjects.store') }}" class="stack-form">
            @csrf
            <div class="input-row">
                <label>
                    <span>Subject name</span>
                    <input name="name" value="{{ old('name') }}" required maxlength="255" autofocus>
                </label>
                <label>
                    <span>Description <small>(optional)</small></span>
                    <textarea name="description" rows="4" maxlength="10000">{{ old('description') }}</textarea>
                </label>
            </div>
            <div class="form-actions">
                <button type="submit">Save subject</button>
                <a class="button-link" href="{{ route('subject.index') }}">Cancel</a>
            </div>
        </form>
    </section>
</x-layout>
