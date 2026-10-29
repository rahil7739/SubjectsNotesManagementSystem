<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $expectedRole = RoleEnum::tryFrom($role);

        abort_unless($expectedRole !== null && $request->user()?->role === $expectedRole, 403);

        return $next($request);
    }
}
