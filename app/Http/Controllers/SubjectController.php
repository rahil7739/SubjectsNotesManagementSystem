<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\StudyActivityNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = auth('web')->user()
            ->subjects()
            ->withCount('notes')
            ->latest()
            ->get();

        return view('subjects.list', ['subjects' => $subjects]);
    }

    public function create(): View
    {
        return view('subjects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
        ]);
        $subject = $request->user('web')->subjects()->create($data);
        User::where('role', RoleEnum::Admin)->get()->each(
            fn (User $admin) => $admin->notify(new StudyActivityNotification(
                'A user created a subject',
                $request->user('web')->name.' created "'.$subject->name.'".',
                route('admin.subjects.index'),
                'subject',
            )),
        );

        return to_route('subject.index')->with('status', 'Subject created.');
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $subject = $request->user('web')->subjects()->findOrFail($subject->id);
        $subject->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
        ]));

        return to_route('dashboard')->with('status', 'Subject updated.');
    }

    public function destroy(Request $request, Subject $subject): RedirectResponse
    {
        $request->user('web')->subjects()->findOrFail($subject->id)->delete();

        return to_route('dashboard')->with('status', 'Subject moved to deleted records.');
    }
}
