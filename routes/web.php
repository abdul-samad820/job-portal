<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobRoleController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\EmailController;

// Default page
Route::get('/', function () {
    return view('home');
})->name('user.home');

// ===================== SUPER ADMIN ROUTES =====================
Route::prefix('superadmin')
    ->name('superadmin.')
    ->middleware(['auth:superadmin'])  
    ->group(function () {

    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/admins', [SuperAdminController::class, 'adminList'])->name('admins');
    Route::get('/add-admin', [SuperAdminController::class, 'createForm'])->name('create.form');
    Route::post('/add-admin', [SuperAdminController::class, 'createAdmin'])->name('create');

    Route::post('/logout',[SuperAdminController::class, 'logout'])
        ->name('logout');
});

  Route::view('/superadmin/login','SuperAdmin.login')->name('superadmin.login.view');

   Route::post('/superadmin/login', [SuperAdminController::class, 'login'])
    ->name('superadmin.login');
 

// ===================== ADMIN ROUTES =====================

Route::middleware(['admin'])->prefix('admin')->name('admin.')->controller(AdminController::class)
->group(function () {

    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/profile', 'admin_profile')->name('profile');
    Route::put('/profile/update', 'update_profile')->name('profile.update');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/selected', 'selectedList')->name('selectedList');
     
// Job Category Routes

Route::controller(JobCategoryController::class)->group(function(){

    Route::get('/jobcategory','job_category')->name('job_category');
    Route::post('/jobcategory_create','job_category_create')->name('job_category_create');
    Route::get('/jobcategory_edit/{id}','job_category_edit')->name('job_category_edit');
    Route::put('/jobcategory_update/{id}','job_category_update')->name('job_category_update');
    Route::delete('/jobcategory_delete/{id}','job_category_delete')->name('job_category_delete');
    Route::view('/jobcategory_add','Admin.job_category_add')->name('job_category_add');
});

//role
Route::controller(JobRoleController::class)->group(function(){
    
    Route::get('/job_role','job_role')->name('job_role');
    Route::get('/job_role_add', 'job_role_add')->name('job_role_add');
    Route::post('job_role_create','job_role_create')->name('job_role_create');
    Route::get('job_role_edit/{id}','job_role_edit')->name('job_role_edit');
    Route::put('job_role_update/{id}','job_role_update')->name('job_role_update');
    Route::delete('/job_role_delete/{id}','job_role_delete')->name('job_role_delete');
});

//job
Route::controller(JobController::class)->group(function(){

     Route::get('/job','job')->name('job');
     Route::get('/job_add','job_add')->name('job_add');
     Route::post('/job_create','job_create')->name('job_create');
     Route::get('/job_edit/{id}','job_edit')->name('job_edit');
     Route::put('/job_update/{id}','job_update')->name('job_update');
     Route::delete('/job_delete/{id}','job_delete')->name('job_delete');

});

});

Route::controller(AdminController::class)->group(function () {

    // Admin Register
    Route::view('/admin/register', 'Admin.register')->name('admin.register.view');
    Route::post('/admin/register', 'register')->name('admin.register');
    // Admin Login
    Route::view('/admin/login', 'Admin.login')->name('admin.login.view');
    Route::post('/admin/login', 'login')->name('admin.login');
    // Resume View
    Route::get('/admin/application/{id}/resume', 'viewResume')->name('admin.application.resume');
});

// ===================== USER ROUTES =====================


Route::middleware(['user'])->controller(UserController::class)->prefix('user')->name('user.')->group(function () {
 
     Route::get('/dashboard', 'user_dashboard')->name('dashboard');
     Route::post('/logout', 'userlogout')->name('logout');
     Route::get('/applied-job', 'job_applied')->name('job_applied');
     Route::get('/user_profile','User_profile')->name('profile');
     Route::get('/user_profile_add','add_user_profile')->name('add_profile');
     Route::post('/user_profile_update','update_user_profile')->name('update_profile');
     Route::get('/user_account_settings','account_setting')->name('account_setting');
     Route::post('/user_account_settings/{id}', 'account_setting_update')
    ->name('account_setting_update');
    
     Route::get('/save-job/{id}', 'save_job')->name('save.job');
     Route::get('/unsave-job/{id}', 'unsave_job')->name('unsave.job');
     Route::get('/saved-jobs', 'saved_jobs')->name('saved.jobs');


    });

// This stays outside because it's a different controller
      Route::post('/user_apply_job-application/{id}', [JobApplicationController::class, 'apply_job_application'])->name('apply_job-application');

      Route::get('/jobapplication', [JobApplicationController::class, 'admin_applications'])
      ->name('job_application');

      Route::get('/user/job-filter', [JobController::class, 'user_job_filter'])
    ->name('user.jobs.filter');


//  Public User Routes (No Auth Needed) 

Route::controller(UserController::class)->prefix('user')->name('user.')->group(function () {

        Route::view('/register', 'User.register')->name('register.view');
        Route::post('/register', 'Userregister')->name('register');
        Route::view('/login', 'User.login')->name('login');  
        Route::post('/login', 'userlogin')->name('login.submit');
        Route::get('/job', 'user_jobs')->name('jobs');
        Route::get('/single-job/{id}', 'user_job_single')->name('job_single');
});


Route::controller(JobApplicationController::class)->group(function () {

    Route::get('/user_form_apply/{id}', 'show_apply_job')->name('apply_form_job_application');
    Route::post('/application/update-status/{id}','updateStatus')->name('admin.application.updateStatus');
    Route::get('/admin/application/approve/{id}', 'approve_application')->name('admin.approve.application');

});
