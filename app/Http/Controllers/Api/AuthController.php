<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use App\Services\UserRegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request, UserRegistrationService $registrations): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $registrations->createPendingUser($data);

        return response()->json([
            'message' => 'Registration submitted. Administrator approval is required before signing in.',
        ], 202);
    }

    public function login(Request $request): JsonResponse
    {
        // dd('tttt');
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'role' => ['sometimes', 'in:user,admin'],
            'device_name' => ['sometimes', 'string', 'max:100'],
        ]);

        $user = User::where('email', $data['email'])
            ->where('role', $data['role'] ?? RoleEnum::User->value)
            ->first();
        if ($user === null || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'These credentials do not match our records.']);
        }
        if ($user->role === RoleEnum::User && ! $user->is_approved) {
            throw ValidationException::withMessages(['email' => 'Your account is waiting for administrator approval.']);
        }

        return $this->issueToken($user, $data['device_name'] ?? 'api');
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],
        ]);
        $user->fill(collect($data)->only(['name', 'email'])->all());
        if (isset($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        return response()->json(['data' => $user->fresh()]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->attributes->get('api_token')->delete();

        return response()->json(['message' => 'Token revoked.']);
    }

    private function issueToken(User $user, string $name): JsonResponse
    {
        $plainToken = bin2hex(random_bytes(32));
        $days = max(1, (int) config('services.api.token_lifetime_days', 30));
        $token = $user->apiTokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainToken),
            'expires_at' => now()->addDays($days),
        ]);

        return response()->json([
            'token' => $plainToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->expires_at,
            'user' => $user,
        ], 201);
    }
}
