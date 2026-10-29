<x-layout title="My subjects">
    <section class="panel" aria-labelledby="subject-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Study workspace</p>
                <h1 id="subject-heading">My subjects</h1>
            </div>
            <a class="button-link button-link-primary" href="{{ route('subjects.create') }}">Add subject</a>
        </div>

        @if (session('status'))
            <p class="status-message" role="status">{{ session('status') }}</p>
        @endif

        <div class="subject-table-wrap">
            <table class="subject-table">
                <thead>
                    <tr>
                        <th scope="col">Subject</th>
                        <th scope="col">Description</th>
                        <th scope="col">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subjects as $subject)
                        <tr>
                            <th scope="row">{{ $subject->name }}</th>
                            <td>{{ $subject->description ?: '—' }}</td>
                            <td>{{ $subject->notes_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="subject-empty">No subjects yet. Select “Add subject” to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layout>
