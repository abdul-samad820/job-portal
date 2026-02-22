<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        $timeout = null;
        $guard = null;

        if (Auth::guard('superadmin')->check()) {
            $timeout = 600; // 10 minutes
            $guard = 'superadmin';
        } 
        elseif (Auth::guard('admin')->check()) {
            $timeout = 900; // 15 minutes
            $guard = 'admin';
        } 
        elseif (Auth::guard('user')->check()) {
            $timeout = 1800; // 30 minutes
            $guard = 'user';
        }

        if ($timeout && $guard) {

            $lastActivity = session('last_activity');

            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                
                Auth::guard($guard)->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/')
                    ->with('error', 'Session expired. Please login again.');
            }

            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}