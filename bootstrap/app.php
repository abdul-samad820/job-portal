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
        $middleware->alias([
            'admin' => \App\Http\Middleware\Admin_mid::class,
            'user' => \App\Http\Middleware\User_mid::class,
            'superadmin' => \App\Http\Middleware\SuperAdminOnly::class,
            'role.timeout' => \App\Http\Middleware\RoleSessionTimeout::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            return response()->view('errors.429', [], 429);
        });

    })
    ->withSchedule(function ($schedule) {
        // Runs hourly — checks for admin notifications
        $schedule->command('admin:check-notifications')->hourly();

        // Runs daily at 9am — sends job alert emails to subscribed users
        $schedule->command('jobs:send-alerts')->dailyAt('09:00');

        // Runs daily — cleans up expired job postings
        $schedule->command('jobs:delete-expired')->daily();
    })
    ->create();
