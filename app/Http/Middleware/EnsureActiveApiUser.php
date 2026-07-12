<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureActiveApiUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            $user->currentAccessToken()->delete();

            return response()->json([
                'status' => 'error',
                'message' => 'Your account has been suspended.',
            ], 403);
        }

        return $next($request);
    }
}
