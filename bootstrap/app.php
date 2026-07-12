<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\Admin_mid::class,
            'user' => \App\Http\Middleware\User_mid::class,
            'superadmin' => \App\Http\Middleware\SuperAdminOnly::class,
            'role.timeout' => \App\Http\Middleware\RoleSessionTimeout::class,
            'api.active' => \App\Http\Middleware\EnsureActiveApiUser::class,
            'verified' => \App\Http\Middleware\EnsureUserEmailIsVerified::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            return response()->view('errors.429', [], 429);
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized action.',
                ], 403);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated. Please login first.',
                ], 401);
            }
        });

    })
    ->withSchedule(function ($schedule) {
        // Runs hourly — checks for admin notifications
        $schedule->command('admin:check-notifications')->hourly();

        // Runs daily at 9am — sends job alert emails to subscribed users
        $schedule->command('jobs:send-alerts')->dailyAt('09:00');

        // Runs daily — prevents the notifications table from growing unbounded
        $schedule->command('app:delete-old-notifications')->daily();
    })
    ->create();
