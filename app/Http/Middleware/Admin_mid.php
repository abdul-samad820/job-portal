<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin_mid
{
    public function handle(Request $request, Closure $next)
    {
        Auth::shouldUse('admin');

        if (! Auth::guard('admin')->check()) {
            return redirect()->route('admin.login.view')
                ->with('error', 'Please login as admin!');
        }

        $admin = Auth::guard('admin')->user();

        // Role check
        if (! $admin || $admin->role !== 'admin') {
            abort(403, 'Access Denied: Only admin can access this section.');
        }

        // Suspend check
        if (! $admin->is_active) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login.view')
                ->with('error', 'Your account has been suspended. Please contact SuperAdmin.');
        }

        return $next($request);
    }
}
