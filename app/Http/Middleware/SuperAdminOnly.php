<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SuperAdminOnly
{
   public function handle(Request $request, Closure $next)
{
    Auth::shouldUse('superadmin');

    if (!Auth::guard('superadmin')->check()) {
        return redirect()->route('superadmin.login.view');
    }

    if (Auth::guard('superadmin')->user()->role !== 'super_admin') {
        abort(403, 'Only super admin allowed!');
    }

    return $next($request);
}

}

