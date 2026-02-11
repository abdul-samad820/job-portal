<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Admin_mid
{
   public function handle(Request $request, Closure $next)
{
    Auth::shouldUse('admin');

    if (!Auth::guard('admin')->check()) {
        return redirect()->route('admin.login.view')
            ->with('error', 'Please login as admin!');
    }

    if (Auth::guard('admin')->user()->role !== 'admin') {
        return abort(403, 'Access Denied: Only admin can access this section.');
    }

    return $next($request);
}
}