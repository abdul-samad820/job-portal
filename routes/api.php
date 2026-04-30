<?php

use App\Http\Controllers\Api\ApplicationApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\JobApiController;
use App\Http\Controllers\Api\ProfileApiController;
use Illuminate\Support\Facades\Route;

/*
|─────────────────────────────────────────────────────
| API Version 1 — Job Portal REST API
|─────────────────────────────────────────────────────
|
| Base URL: /api/v1/
| Auth: Bearer Token (Sanctum)
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ══════════════════════════════════════
    // PUBLIC ROUTES — Token ki zaroorat nahi
    // ══════════════════════════════════════
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [AuthApiController::class, 'register'])->name('register');
        Route::post('/login', [AuthApiController::class, 'login'])->name('login');
    });

    // Public jobs list
    Route::get('/jobs', [JobApiController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{id}', [JobApiController::class, 'show'])->name('jobs.show');

    // ══════════════════════════════════════
    // PROTECTED ROUTES — Token required
    // ══════════════════════════════════════
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthApiController::class, 'logout'])->name('auth.logout');
        Route::get('/auth/me', [AuthApiController::class, 'me'])->name('auth.me');

        // Profile
        Route::get('/profile', [ProfileApiController::class, 'show'])->name('profile.show');
        Route::post('/profile', [ProfileApiController::class, 'update'])->name('profile.update');

        // Applications
        Route::get('/applications', [ApplicationApiController::class, 'index'])->name('applications.index');
        Route::post('/jobs/{id}/apply', [ApplicationApiController::class, 'apply'])->name('applications.apply');
        // Route::delete('/applications/{id}', [ApplicationApiController::class, 'withdraw'])->name('applications.withdraw');

        // Saved Jobs
        Route::get('/saved-jobs', [JobApiController::class, 'savedJobs'])->name('jobs.saved');
        Route::post('/jobs/{id}/save', [JobApiController::class, 'saveJob'])->name('jobs.save');
        Route::delete('/jobs/{id}/unsave', [JobApiController::class, 'unsaveJob'])->name('jobs.unsave');
    });
});
