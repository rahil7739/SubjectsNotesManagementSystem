<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.api.key');
        $provided = $request->header('X-API-Key');

        if (! is_string($expected) || $expected === '') {
            return response()->json([
                'message' => 'API access is not configured. Set API_SHARED_KEY in the server environment.',
            ], 503);
        }

        if (! is_string($provided) || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Invalid API key.'], 401);
        }

        return $next($request);
    }
}
