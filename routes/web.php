<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminExportController;
use App\Http\Controllers\BulkApplyController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CompanyFollowController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\JobAlertController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobReportController;
use App\Http\Controllers\JobRoleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ResumeBuilderController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\ResumeScoreController;
use App\Http\Controllers\SalaryInsightController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SuperAdminBroadcastController;
use App\Http\Controllers\SuperAdminContactController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdminExportController;
use App\Http\Controllers\SuperAdminFaqController;
use App\Http\Controllers\SuperAdminJobController;
use App\Http\Controllers\SuperAdminProfileController;
use App\Http\Controllers\SuperAdminReportController;
use App\Http\Controllers\SuperAdminTestimonialController;
use App\Http\Controllers\SuperAdminUserController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Default page
Route::get('/', function () {
    return view('home');
})->name('user.home');

// Contact form — public, rate-limited via ContactController
Route::post('/contact', [ContactController::class, 'send'])
    ->middleware('throttle:3,60')
    ->name('contact.send');

// Static legal pages — public
Route::view('/privacy-policy', 'legal.privacy_policy')->name('privacy.policy');
Route::view('/terms-conditions', 'legal.terms_conditions')->name('terms.conditions');

// SEO — sitemap + robots
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Public company profile page
Route::get('/company/{slug}', [CompanyProfileController::class, 'show'])->name('company.show');

// ===================== SUPER ADMIN ROUTES =====================
Route::prefix('superadmin')->name('superadmin.')->middleware(['superadmin', 'role.timeout'])
    ->group(function () {

        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/admins', [SuperAdminController::class, 'adminList'])->name('admins');

        // Global site settings (contact info, social links) — SuperAdmin only.
        Route::get('/settings', [SuperAdminController::class, 'settingsIndex'])->name('settings');
        Route::put('/settings', [SuperAdminController::class, 'settingsUpdate'])->name('settings.update');
        Route::get('/add-admin', [SuperAdminController::class, 'createForm'])->name('create.form');
        Route::post('/add-admin', [SuperAdminController::class, 'createAdmin'])->name('create');
        Route::post('/logout', [SuperAdminController::class, 'logout'])->name('logout');

        Route::patch('/admin/{id}/suspend', [SuperAdminController::class, 'suspendAdmin'])
            ->name('admin.suspend');
        Route::patch('/admin/{id}/unsuspend', [SuperAdminController::class, 'unsuspendAdmin'])
            ->name('admin.unsuspend');
        Route::patch('/admin/{id}/verify', [SuperAdminController::class, 'verifyAdmin'])
            ->name('admin.verify');
        Route::patch('/admin/{id}/unverify', [SuperAdminController::class, 'unverifyAdmin'])
            ->name('admin.unverify');
        Route::delete('/admin/{id}/delete', [SuperAdminController::class, 'deleteAdmin'])
            ->name('admin.delete');
        Route::get('/admin/{id}/show', [SuperAdminController::class, 'showAdmin'])
            ->name('admin.show');
        Route::get('/admins/export', [SuperAdminController::class, 'exportAdminsCsv'])->name('admins.export');
        Route::post('/admins/bulk', [SuperAdminController::class, 'bulkAction'])->name('admins.bulk');
        Route::get('/jobs/export', [SuperAdminExportController::class, 'jobs'])->name('jobs.export');

        // ── Platform-wide broadcast announcement ────────────
        Route::get('/broadcast', [SuperAdminBroadcastController::class, 'create'])->name('broadcast');
        Route::post('/broadcast', [SuperAdminBroadcastController::class, 'send'])->name('broadcast.send');

        // ── SuperAdmin's own profile (email/password) ───────────
        Route::get('/profile', [SuperAdminProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [SuperAdminProfileController::class, 'update'])->name('profile.update');

        // ── Phase 3: Edit admin + activity log ──────────────────────────
        Route::get('/admin/{id}/edit', [SuperAdminController::class, 'editForm'])->name('admin.edit');
        Route::put('/admin/{id}', [SuperAdminController::class, 'updateAdmin'])->name('admin.update');
        Route::get('/activity-log', [SuperAdminController::class, 'activityLog'])->name('activity-log');

        // ── Phase 4: Analytics/reports + user directory ───────
        Route::get('/reports', [SuperAdminReportController::class, 'index'])->name('reports');
        Route::get('/users', [SuperAdminUserController::class, 'index'])->name('users');
        Route::get('/users/export', [SuperAdminUserController::class, 'exportCsv'])->name('users.export');
        Route::patch('/users/{id}/suspend', [SuperAdminUserController::class, 'suspend'])->name('users.suspend');
        Route::patch('/users/{id}/unsuspend', [SuperAdminUserController::class, 'unsuspend'])->name('users.unsuspend');

        // ── Phase 1: Global FAQ management (admin_id = null only) ──────
        Route::get('/faqs', [SuperAdminFaqController::class, 'index'])->name('faqs');
        Route::post('/faqs', [SuperAdminFaqController::class, 'store'])->name('faqs.store');
        Route::put('/faqs/{id}', [SuperAdminFaqController::class, 'update'])->name('faqs.update');
        Route::patch('/faqs/{id}/toggle', [SuperAdminFaqController::class, 'toggle'])->name('faqs.toggle');
        Route::delete('/faqs/{id}', [SuperAdminFaqController::class, 'destroy'])->name('faqs.destroy');

        // ── Phase 1: Testimonial oversight across ALL admins ────────────
        Route::get('/testimonials', [SuperAdminTestimonialController::class, 'index'])->name('testimonials');
        Route::patch('/testimonials/{id}/approve', [SuperAdminTestimonialController::class, 'approve'])->name('testimonials.approve');
        Route::patch('/testimonials/{id}/reject', [SuperAdminTestimonialController::class, 'reject'])->name('testimonials.reject');
        Route::delete('/testimonials/{id}', [SuperAdminTestimonialController::class, 'destroy'])->name('testimonials.destroy');

        // ── Phase 2: Job moderation across ALL companies ─────────────────
        Route::get('/jobs', [SuperAdminJobController::class, 'index'])->name('jobs');
        Route::patch('/jobs/{id}/hide', [SuperAdminJobController::class, 'hide'])->name('jobs.hide');
        Route::patch('/jobs/{id}/unhide', [SuperAdminJobController::class, 'unhide'])->name('jobs.unhide');
        Route::delete('/jobs/{id}', [SuperAdminJobController::class, 'destroy'])->name('jobs.destroy');

        // ── Phase 2: Reported jobs queue ──────────────────────────────────
        Route::get('/job-reports', [SuperAdminJobController::class, 'reports'])->name('job-reports');
        Route::patch('/job-reports/{id}/dismiss', [SuperAdminJobController::class, 'dismissReport'])->name('job-reports.dismiss');
        Route::patch('/job-reports/{id}/action', [SuperAdminJobController::class, 'actionReport'])->name('job-reports.action');

        // ── Notifications ────────────────────────────────────────────────
        Route::post('/notifications/read', [SuperAdminController::class, 'readNotifications'])
            ->name('notifications.read');

        // ── Phase 1: Contact form inbox ──────────────────────────────────
        Route::get('/contact-messages', [SuperAdminContactController::class, 'index'])->name('contact.index');
        Route::get('/contact-messages/{id}', [SuperAdminContactController::class, 'show'])->name('contact.show');
        Route::delete('/contact-messages/{id}', [SuperAdminContactController::class, 'destroy'])->name('contact.destroy');
    });

Route::view('/superadmin/login', 'SuperAdmin.login')->name('superadmin.login.view');
Route::post('/superadmin/login', [SuperAdminController::class, 'login'])->middleware('throttle:3,1')
    ->name('superadmin.login');

Route::get('/superadmin/verify-2fa', [SuperAdminController::class, 'twoFactorForm'])->name('superadmin.2fa.form');
Route::post('/superadmin/verify-2fa', [SuperAdminController::class, 'twoFactorVerify'])
    ->middleware('throttle:5,1')
    ->name('superadmin.2fa.verify');
Route::post('/superadmin/verify-2fa/resend', [SuperAdminController::class, 'twoFactorResend'])
    ->middleware('throttle:3,1')
    ->name('superadmin.2fa.resend');

// ===================== ADMIN ROUTES =====================

Route::middleware(['admin', 'role.timeout'])->prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {

    Route::get('/search', 'search')->name('search');

    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/profile', 'admin_profile')->name('profile');
    Route::put('/profile/update', 'update_profile')->name('profile.update');
    Route::get('/data-export', 'dataExport')->name('data_export');
    Route::delete('/account', 'deleteAccount')->name('account.delete');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/force-password-change', 'forcePasswordChangeForm')->name('force_password.change');
    Route::post('/force-password-change', 'forcePasswordChangeUpdate')->name('force_password.update');
    Route::get('/activity-log', 'activityLog')->name('activity_log');
    Route::get('/sessions', 'mySessions')->name('sessions');
    Route::delete('/sessions/{sessionId}', 'revokeSession')->name('sessions.revoke');
    Route::delete('/sessions', 'revokeOtherSessions')->name('sessions.revokeOthers');
    Route::get('/selected', 'selectedList')->name('selectedList');
    Route::get('/selected/export', 'exportSelectedList')->name('selectedList.export');
    Route::post('/notifications/read', 'readNotifications')
        ->name('notifications.read');

    Route::controller(AdminExportController::class)->prefix('export')->name('export.')->group(function () {
        Route::get('/jobs', 'jobs')->name('jobs');
        Route::get('/applications', 'applications')->name('applications');
        Route::get('/candidates', 'candidates')->name('candidates');
    });

    Route::get('/admin/application/{id}/resume',
        [JobApplicationController::class, 'viewResume'])
        ->name('application.resume');

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

    // candidates (open-to-work browsing + invites)
    Route::controller(CandidateController::class)->prefix('candidates')->name('candidates.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{userId}/invite', 'invite')->name('invite');
    });

});
Route::middleware(['admin', 'role.timeout'])->prefix('admin')->group(function () {

    Route::post('/application/{id}/note',
        [JobApplicationController::class, 'updateNote'])->name('admin.application.note');
});

Route::controller(AdminController::class)->group(function () {

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
    Route::get('/data-export', 'dataExport')->name('data_export');
    Route::delete('/account', 'deleteAccount')->name('account.delete');
    Route::get('/saved-jobs', 'saved_jobs')->name('saved.jobs');
    Route::post('/open-to-work/toggle', 'toggleOpenToWork')->name('open_to_work.toggle');
    Route::get('/activity-log', 'activityLog')->name('activity_log');
    Route::get('/invites', 'invites')->name('invites');
    Route::post('/notifications/read', 'readNotifications')
        ->name('notifications.read');

});

Route::middleware(['user', 'role.timeout', 'verified'])
    ->controller(TestimonialController::class)
    ->name('user.')
    ->group(function () {
        Route::get('/user/applied-job/{applicationId}/review', 'write_review')->name('review.create');
        Route::post('/user/applied-job/{applicationId}/review', 'submit_review')->name('review.store');
    });

Route::middleware(['user', 'role.timeout', 'verified'])
    ->get('/my-interviews', [InterviewController::class, 'userInterviews'])
    ->name('user.interviews');

Route::middleware(['user', 'role.timeout', 'verified'])->group(function () {
    Route::post('/saved-jobs/{job}', [UserController::class, 'saveJob'])->name('saved.store');
    Route::delete('/saved-jobs/{job}', [UserController::class, 'unsaveJob'])->name('saved.destroy');
    Route::get('/saved-jobs/{job}/apply', [JobApplicationController::class, 'applyFromSaved'])->name('apply.from.saved');
    Route::delete('/applied-job/{id}/withdraw', [JobApplicationController::class, 'withdraw'])->name('application.withdraw');
    Route::get('/applied-job/{id}/timeline', [JobApplicationController::class, 'timeline'])->name('application.timeline');

});

// ===================== RESUME LIBRARY ROUTES =====================
Route::middleware(['user', 'role.timeout', 'verified'])
    ->controller(ResumeController::class)
    ->prefix('user/resumes')
    ->name('user.resumes.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::patch('/{id}/default', 'setDefault')->name('default');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('/{id}/download', 'download')->name('download');
    });

// ===================== BULK APPLY ROUTES =====================
Route::middleware(['user', 'role.timeout', 'verified'])
    ->controller(BulkApplyController::class)
    ->prefix('user/bulk-apply')
    ->name('user.bulk_apply.')
    ->group(function () {
        Route::get('/', 'form')->name('form');
        Route::post('/', 'store')->name('store');
    });

// ===================== COMPANY FOLLOW ROUTES =====================
Route::middleware(['user', 'role.timeout', 'verified'])
    ->controller(CompanyFollowController::class)
    ->prefix('user/following')
    ->name('user.following.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{adminId}/toggle', 'toggle')->name('toggle');
    });

// ===================== REFERRAL ROUTES =====================
Route::middleware(['user', 'role.timeout', 'verified'])
    ->get('/user/referrals', [ReferralController::class, 'index'])
    ->name('user.referrals');

// ===================== SALARY INSIGHTS ROUTES =====================
Route::middleware(['user', 'role.timeout', 'verified'])
    ->get('/user/salary-insights', [SalaryInsightController::class, 'index'])
    ->name('user.salary_insights');

// ===================== JOB COMPARE ROUTE =====================
Route::middleware(['user', 'role.timeout', 'verified'])
    ->get('/user/compare', [CompareController::class, 'index'])
    ->name('user.compare');

// ===================== RESUME SCORE / ATS CHECK ROUTE =====================
Route::middleware(['user', 'role.timeout', 'verified'])
    ->get('/user/resume-score/{jobId?}', [ResumeScoreController::class, 'index'])
    ->name('user.resume_score');

// ===================== RESUME BUILDER ROUTE =====================
Route::middleware(['user', 'role.timeout', 'verified'])
    ->get('/user/resume-builder', [ResumeBuilderController::class, 'show'])
    ->name('user.resume_builder');

Route::middleware(['user', 'role.timeout', 'verified'])->group(function () {
    Route::post('/apply-job/{job}', [JobApplicationController::class, 'apply'])
        ->name('apply_job_application');

    Route::post('/jobs/{job}/report', [JobReportController::class, 'store'])
        ->name('jobs.report');
});

Route::middleware(['admin', 'role.timeout'])
    ->get('/admin/applications', [JobApplicationController::class, 'jobapplications'])
    ->name('job_application');

Route::get('/user/job-filter', [JobController::class, 'user_job_filter'])->name('user.jobs.filter');

//  Public User Routes (No Auth Needed)

Route::controller(UserController::class)->prefix('user')->name('user.')->group(function () {

    Route::get('/register', [UserController::class, 'registerView'])->name('register.view');
    Route::post('/register', 'userregister')->name('register');
    Route::view('/login', 'User.login')->name('login');
    Route::post('/login', 'userlogin')->middleware('throttle:5,1')->name('login.submit');
    Route::get('/job', 'user_jobs')->name('jobs');
    Route::get('/single-job/{id}', 'user_job_single')->name('job_single');
});

Route::middleware(['user', 'role.timeout', 'verified'])
    ->get('/user_form_apply/{id}', [JobApplicationController::class, 'show_apply_job'])
    ->name('apply_form_job_application');

Route::middleware(['admin', 'role.timeout'])
    ->post('/application/update-status/{id}', [JobApplicationController::class, 'updateStatus'])
    ->name('admin.application.updateStatus');

Route::middleware(['admin', 'role.timeout'])
    ->post('/admin/applications/bulk-update-status', [JobApplicationController::class, 'bulkUpdateStatus'])
    ->name('admin.application.bulkUpdateStatus');

Route::middleware(['admin', 'role.timeout'])
    ->get('/admin/application/{id}/download-resume', [JobApplicationController::class, 'downloadResume'])
    ->name('admin.resume.download');

Route::middleware(['admin', 'role.timeout'])->controller(FaqController::class)->group(function () {

    Route::get('/faq', 'faqs')->name('faq');
    Route::post('/faq/create', 'faqs_create')->name('faqs_create');
    Route::get('/faqs_edit/{id}', 'faqs_edit')->name('faqs_edit');
    Route::put('/faqs_update/{id}', 'faqs_update')->name('faqs_update');
    Route::delete('/faqs_delete/{id}', 'faqs_delete')->name('faqs_delete');
});

Route::middleware(['admin', 'role.timeout'])->controller(TestimonialController::class)->group(function () {

    Route::get('/admin/testimonials', 'testimonials')->name('admin.testimonials');
    Route::post('/admin/testimonials/create', 'testimonials_create')->name('testimonials_create');
    Route::get('/admin/testimonials_edit/{id}', 'testimonials_edit')->name('testimonials_edit');
    Route::put('/admin/testimonials_update/{id}', 'testimonials_update')->name('testimonials_update');
    Route::delete('/admin/testimonials_delete/{id}', 'testimonials_delete')->name('testimonials_delete');
    Route::patch('/admin/testimonials/{id}/approve', 'testimonials_approve')->name('testimonials_approve');
    Route::patch('/admin/testimonials/{id}/reject', 'testimonials_reject')->name('testimonials_reject');
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
Route::middleware(['user', 'role.timeout'])
    ->controller(JobAlertController::class)
    ->prefix('job-alerts')
    ->name('job.alert.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/save', 'save')->name('save');
        Route::patch('/toggle', 'toggle')->name('toggle');
        Route::delete('/destroy', 'destroy')->name('destroy');
    });

Route::middleware(['auth:admin,superadmin,user', 'role.timeout'])
    ->get('/notifications/{id}/open', [NotificationController::class, 'open'])
    ->name('notifications.open');

    Route::get('/test-error/{code}', function ($code) {
    abort((int) $code);
});