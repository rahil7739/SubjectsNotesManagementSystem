<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('profile', ['profileUser' => $request->user('web'), 'isAdminProfile' => false]);
    }

    public function showAdmin(Request $request): View
    {
        return view('profile', ['profileUser' => $request->user('admin'), 'isAdminProfile' => true]);
    }

    public function update(Request $request): RedirectResponse
    {
        return $this->updateFor($request, 'web');
    }

    public function updateAdmin(Request $request): RedirectResponse
    {
        return $this->updateFor($request, 'admin');
    }

    private function updateFor(Request $request, string $guard): RedirectResponse
    {
        $user = $request->user($guard);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }
        $user->save();

        return to_route($guard === 'admin' ? 'admin.profile.show' : 'profile.show')
            ->with('status', 'Profile updated.');
    }
}
