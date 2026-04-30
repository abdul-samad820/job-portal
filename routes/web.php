<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPasswordResetController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\JobAlertController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobRoleController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Default page
Route::get('/', function () {
    return view('home');
})->name('user.home');

// ===================== SUPER ADMIN ROUTES =====================
Route::prefix('superadmin')->name('superadmin.')->middleware(['superadmin', 'role.timeout'])
    ->group(function () {

        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/admins', [SuperAdminController::class, 'adminList'])->name('admins');
        Route::get('/add-admin', [SuperAdminController::class, 'createForm'])->name('create.form');
        Route::post('/add-admin', [SuperAdminController::class, 'createAdmin'])->name('create');
        Route::post('/logout', [SuperAdminController::class, 'logout'])->name('logout');

        Route::patch('/admin/{id}/suspend', [SuperAdminController::class, 'suspendAdmin'])
            ->name('admin.suspend');
        Route::patch('/admin/{id}/unsuspend', [SuperAdminController::class, 'unsuspendAdmin'])
            ->name('admin.unsuspend');
        Route::delete('/admin/{id}/delete', [SuperAdminController::class, 'deleteAdmin'])
            ->name('admin.delete');
        Route::get('/admin/{id}/show', [SuperAdminController::class, 'showAdmin'])
            ->name('admin.show');
    });

Route::view('/superadmin/login', 'SuperAdmin.login')->name('superadmin.login.view');
Route::post('/superadmin/login', [SuperAdminController::class, 'login'])->middleware('throttle:3,1')
    ->name('superadmin.login');

// ===================== ADMIN PASSWORD RESET =====================
Route::middleware('guest:admin')->group(function () {

    Route::get('/admin/forgot-password',
        [AdminPasswordResetController::class, 'showForgotForm'])
        ->name('admin.password.request');

    Route::post('/admin/forgot-password',
        [AdminPasswordResetController::class, 'sendResetLink'])
        ->name('admin.password.email')
        ->middleware('throttle:5,1');

    Route::get('/admin/reset-password/{token}',
        [AdminPasswordResetController::class, 'showResetForm'])
        ->name('admin.password.reset');

    Route::post('/admin/reset-password',
        [AdminPasswordResetController::class, 'resetPassword'])
        ->name('admin.password.update');
});
// ===================== ADMIN ROUTES =====================

Route::middleware(['admin', 'role.timeout'])->prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {

    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/profile', 'admin_profile')->name('profile');
    Route::put('/profile/update', 'update_profile')->name('profile.update');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/selected', 'selectedList')->name('selectedList');
    Route::post('/notifications/read', 'readNotifications')
        ->name('notifications.read');

   
    Route::get('/admin/application/{id}/resume',
        [JobApplicationController::class, 'viewResume'])
        ->name('admin.application.resume');

    // Job Category Routes

    Route::controller(JobCategoryController::class)->group(function () {

        Route::get('/jobcategory', 'job_category')->name('job_category');
        Route::post('/jobcategory_create', 'job_category_create')->name('job_category_create');
        Route::get('/jobcategory_edit/{id}', 'job_category_edit')->name('job_category_edit');
        Route::put('/jobcategory_update/{id}', 'job_category_update')->name('job_category_update');
        Route::delete('/jobcategory_delete/{id}', 'job_category_delete')->name('job_category_delete');
        Route::view('/jobcategory_add', 'Admin.job_category_add')->name('job_category_add');
    });
    // Interview Routes — Admin
    Route::controller(InterviewController::class)
        ->prefix('interview')
        ->name('interview.')
        ->group(function () {
            Route::get('/schedule/{applicationId}', 'create')->name('create');
            Route::post('/schedule/{applicationId}', 'store')->name('store');
            Route::patch('/cancel/{interviewId}', 'cancel')->name('cancel');
            Route::patch('/complete/{interviewId}', 'complete')->name('complete');
        });
    // role
    Route::controller(JobRoleController::class)->group(function () {

        Route::get('/job_role', 'job_role')->name('job_role');
        Route::get('/job_role_add', 'job_role_add')->name('job_role_add');
        Route::post('job_role_create', 'job_role_create')->name('job_role_create');
        Route::get('job_role_edit/{id}', 'job_role_edit')->name('job_role_edit');
        Route::put('job_role_update/{id}', 'job_role_update')->name('job_role_update');
        Route::delete('/job_role_delete/{id}', 'job_role_delete')->name('job_role_delete');
    });

    // job
    Route::controller(JobController::class)->group(function () {

        Route::get('/job', 'job')->name('job');
        Route::get('/job_add', 'job_add')->name('job_add');
        Route::post('/job_create', 'job_create')->name('job_create');
        Route::get('/job_edit/{id}', 'job_edit')->name('job_edit');
        Route::put('/job_update/{id}', 'job_update')->name('job_update');
        Route::delete('/job_delete/{id}', 'job_delete')->name('job_delete');

    });

});
Route::middleware(['admin', 'role.timeout'])->prefix('admin')->group(function () {

    Route::post('/application/{id}/note',
        [JobApplicationController::class, 'updateNote'])->name('admin.application.note');
});

Route::controller(AdminController::class)->group(function () {

    // Admin Register
    Route::view('/admin/register', 'Admin.register')->name('admin.register.view');
    Route::post('/admin/register', 'register')->name('admin.register');
    // Admin Login
    Route::view('/admin/login', 'Admin.login')->name('admin.login.view');
    Route::post('/admin/login', 'login')->middleware('throttle:3,1')->name('admin.login');
});

// ===================== USER ROUTES =====================

Route::middleware(['user', 'role.timeout', 'verified'])->controller(UserController::class)->prefix('user')->name('user.')->group(function () {

    Route::get('/dashboard', 'user_dashboard')->name('dashboard');
    Route::post('/logout', 'userlogout')->name('logout');
    Route::get('/applied-job', 'job_applied')->name('job_applied');
    Route::get('/user_profile', 'User_profile')->name('profile');
    Route::get('/user_profile_add', 'add_user_profile')->name('add_profile');
    Route::post('/user_profile_update', 'update_user_profile')->name('update_profile');
    Route::get('/user_account_settings', 'account_setting')->name('account_setting');
    Route::post('/user_account_settings', 'account_setting_update')
        ->name('account_setting_update');
    Route::get('/saved-jobs', 'saved_jobs')->name('saved.jobs');
    Route::post('/notifications/read', 'readNotifications')
        ->name('notifications.read');

});

Route::middleware(['user', 'role.timeout', 'verified'])
    ->get('/my-interviews', [InterviewController::class, 'userInterviews'])
    ->name('user.interviews');

Route::middleware(['user', 'role.timeout', 'verified'])->group(function () {
    Route::post('/saved-jobs/{job}', [UserController::class, 'saveJob'])->name('saved.store');
    Route::delete('/saved-jobs/{job}', [UserController::class, 'unsaveJob'])->name('saved.destroy');

});

Route::middleware(['user', 'verified'])->group(function () {
    Route::post('/apply-job/{job}', [JobApplicationController::class, 'apply'])
        ->name('apply_job_application');
});

Route::middleware(['admin', 'role.timeout'])
    ->get('/admin/applications', [JobApplicationController::class, 'admin_applications'])
    ->name('job_application');

Route::get('/user/job-filter', [JobController::class, 'user_job_filter'])->name('user.jobs.filter');

//  Public User Routes (No Auth Needed)

Route::controller(UserController::class)->prefix('user')->name('user.')->group(function () {

    Route::view('/register', 'User.register')->name('register.view');
    Route::post('/register', 'userregister')->name('register');
    Route::view('/login', 'User.login')->name('login');
    Route::post('/login', 'userlogin')->middleware('throttle:5,1')->name('login.submit');
    Route::get('/job', 'user_jobs')->name('jobs');
    Route::get('/single-job/{id}', 'user_job_single')->name('job_single');
});

Route::controller(JobApplicationController::class)->group(function () {

    Route::get('/user_form_apply/{id}', 'show_apply_job')->name('apply_form_job_application');
    Route::post('/application/update-status/{id}', 'updateStatus')->name('admin.application.updateStatus');
    Route::get('/admin/application/approve/{id}', 'approve_application')->name('admin.approve.application');

});

Route::post('/admin/notifications/read', function () {
    auth('admin')->user()->unreadNotifications->markAsRead();
    return back();
})->name('admin.notifications.read');

Route::get('/admin/application/{id}/download-resume',
    [JobApplicationController::class, 'downloadResume'])->name('admin.resume.download');

Route::controller(FaqController::class)->group(function () {

    Route::get('/faq', 'faqs')->name('faq');
    Route::post('/faq/create', 'faqs_create')->name('faqs_create');
    Route::get('/faqs_edit/{id}', 'faqs_edit')->name('faqs_edit');
    Route::put('/faqs_update/{id}', 'faqs_update')->name('faqs_update');
    Route::delete('/faqs_delete/{id}', 'faqs_delete')->name('faqs_delete');
});

// ===================== EMAIL VERIFICATION ROUTES =====================

// Verification notice page
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth:user')->name('verification.notice');

// Verification link click handler
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {

    if (! Auth::guard('user')->check()) {
        return redirect()->route('user.login');
    }
    $request->fulfill();

    return redirect()->route('user.dashboard')
        ->with('success', 'Email verified successfully!');
})->middleware(['auth:user', 'signed'])->name('verification.verify');

// Resend verification email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user('user')->sendEmailVerificationNotification();

    return back()->with('success', 'Verification link sent to your email!');
})->middleware(['auth:user', 'throttle:3,1'])->name('verification.send');

// ===================== PASSWORD RESET ROUTES =====================


Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])
    ->middleware('guest:user')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware(['guest:user', 'throttle:3,1'])
    ->name('password.email');

Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->middleware('guest:user')
    ->name('password.reset');

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->middleware('guest:user')
    ->name('password.update');

// Job Alert Routes
Route::controller(JobAlertController::class)
    ->prefix('job-alerts')
    ->name('job.alert.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/save', 'save')->name('save');
        Route::patch('/toggle', 'toggle')->name('toggle');
        Route::delete('/destroy', 'destroy')->name('destroy');
    });
