<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class User_mid
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('user')->check()) {
            return redirect()->route('user.login')->with('error', 'Access denied! Please login first.');
        }

        $user = Auth::guard('user')->user();

        if (! $user->is_active) {
            Auth::guard('user')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('user.login')
                ->with('error', 'Your account has been suspended. Please contact support.');
        }

        return $next($request);
    }
}
