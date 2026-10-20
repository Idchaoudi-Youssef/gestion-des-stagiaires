<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to one or more user roles.
 *
 * Usage in routes/web.php:
 *   Route::middleware('role:admin')->group(...)
 *   Route::middleware('role:admin,supervisor')->group(...)
 *
 * The role is read from the database on every request, so revoking a role
 * takes effect immediately instead of waiting for the session to expire.
 */
class EnsureUserType
{
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403, 'You are not allowed to access this page.');
        }

        $allowed = array_map(
            static fn (string $type): string => UserType::tryFrom($type)?->value ?? $type,
            $types
        );

        if (! in_array($user->usertype->value, $allowed, true)) {
            abort(403, 'You are not allowed to access this page.');
        }

        return $next($request);
    }
}
