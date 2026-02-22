<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Admin_mid; 
use App\Http\Middleware\User_mid;
use App\Http\Middleware\SuperAdminOnly;
 
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withSchedule(function ($schedule) {
        $schedule->command('admin:check-notifications')
                 ->hourly();
    })

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\Admin_mid::class,
            'user'  => \App\Http\Middleware\User_mid::class,
            'superadmin' => \App\Http\Middleware\SuperAdminOnly::class,
             'role.timeout' => \App\Http\Middleware\RoleSessionTimeout::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
    })
    ->create();