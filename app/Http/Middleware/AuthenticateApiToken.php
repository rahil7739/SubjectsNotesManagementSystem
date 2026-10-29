<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();
        $token = is_string($plainToken)
            ? ApiToken::with('user')->where('token', hash('sha256', $plainToken))->first()
            : null;

        if ($token === null || ($token->expires_at !== null && $token->expires_at->isPast())) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($token->user === null || $token->user->trashed()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();
        $request->setUserResolver(fn () => $token->user);
        $request->attributes->set('api_token', $token);

        return $next($request);
    }
}
