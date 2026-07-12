<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Drop-in replacement for Laravel's built-in "verified" middleware
 * (Illuminate\Auth\Middleware\EnsureEmailIsVerified).
 *
 * The built-in version checks $request->user() with NO guard argument,
 * which resolves against the app's *default* auth guard. This app logs
 * users in via the 'user' guard specifically, so the built-in check
 * always saw a null user and redirected verified, logged-in users back
 * to the verification-notice page in an endless loop. This version
 * checks the 'user' guard explicitly instead.
 */
class EnsureUserEmailIsVerified
{
    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null): Response
    {
        $user = Auth::guard('user')->user();

        if (! $user ||
            ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail())) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'));
        }

        return $next($request);
    }
}
