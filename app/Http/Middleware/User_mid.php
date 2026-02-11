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
     if (!Auth::guard('user')->check()) {
            return redirect()->route('user.login')->with('error', 'Access denied! Please login first.');
     }
        return $next($request);
    }
}
