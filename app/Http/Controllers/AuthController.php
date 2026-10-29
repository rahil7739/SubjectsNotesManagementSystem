<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\UserRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showUserLogin(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login', ['guard' => 'web']);
    }

    public function showAdminLogin(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login', ['guard' => 'admin']);
    }

    public function register(Request $request, UserRegistrationService $registrations): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $registrations->createPendingUser($validated);

        return to_route('login')->with('status', 'Your registration was sent to an administrator. You can sign in after it is approved.');
    }

    public function loginUser(Request $request): RedirectResponse
    {
        return $this->authenticate($request, 'web', RoleEnum::User, 'dashboard');
    }

    public function loginAdmin(Request $request): RedirectResponse
    {
        return $this->authenticate($request, 'admin', RoleEnum::Admin, 'admin.dashboard');
    }

    public function logoutUser(Request $request): RedirectResponse
    {
        return $this->logout($request, 'web', 'login');
    }

    public function logoutAdmin(Request $request): RedirectResponse
    {
        return $this->logout($request, 'admin', 'admin.login');
    }

    private function authenticate(Request $request, string $guard, RoleEnum $role, string $destination): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);
        $user = User::where('email', $credentials['email'])
            ->where('role', $role)
            ->first();

        if ($role === RoleEnum::User && $user !== null && ! $user->is_approved) {
            throw ValidationException::withMessages([
                'email' => 'Your account is waiting for administrator approval.',
            ]);
        }

        $key = $guard.'|'.Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many login attempts. Please try again in '.RateLimiter::availableIn($key).' seconds.',
            ]);
        }

        if (! Auth::guard($guard)->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'role' => $role->value,
        ], $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route($destination));
    }

    private function logout(Request $request, string $guard, string $destination): RedirectResponse
    {
        Auth::guard($guard)->logout();
        $request->session()->regenerate();

        return redirect()->route($destination);
    }
}
