<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\User_profile;
use App\Mail\ApplicationStatusMail;
use Illuminate\Support\Facades\Auth;
use App\Models\SavedJob;
use App\Notifications\NewJobApplicationNotification;
use App\Notifications\ApplicationStatusNotification;

class JobApplicationController extends Controller
{

    public function show_apply_job($id)
    {
        $job = Job::with('admin')->findOrFail($id);
        $alreadyApplied = JobApplication::where('user_id', Auth::guard('user')->id())
                    ->where('job_id', $id)
                    ->exists();

        return view('User.user_job_application', compact('job','alreadyApplied'));
    }

 public function apply_job_application(Request $request, $id)
{
    $user_id = Auth::guard('user')->id();
    
    // if (!$user_id) {
    //     return redirect()->route('user.login')
    //         ->with('error', 'Please login first!');
    // }
 
    $job = Job::with('admin')->findOrFail($id);

    // Deadline Check
    if ($job->last_date && now()->greaterThan($job->last_date)) {
        return back()->with('error', 'Application deadline has passed.');
    }

    // Profile Check
    $profile = User_profile::where('user_id', $user_id)->first();

    if (
        !$profile ||
        empty($profile->professional_summary) ||
        empty($profile->core_skills) ||
        empty($profile->education)
    ) {
        return redirect()->route('user.profile')
            ->with('error', 'Please complete your profile before applying.');
    }

    // Already Applied Check
  $alreadyApplied = JobApplication::where('user_id', $user_id)
    ->where('job_id', $id)
    ->first();

if ($alreadyApplied && $alreadyApplied->status !== 'rejected') {
    return back()->with('error', 'You have already applied for this job.');
}

    // Validation
    $request->validate([
        'cover_letter' => 'required|string|min:20|max:500',
        'resume' => 'required|mimes:pdf|max:2048',
    ]);
    $path = $request->file('resume')->store('resumes', 'public');

$application = JobApplication::create([
    'user_id' => $user_id,
    'job_id' => $id,
    'cover_letter' => $request->cover_letter,
    'resume' => ($path), // only filename
    'status' => 'pending',
]); 

    // 🔔 SEND NOTIFICATION (AFTER SUCCESS)
    if ($job->admin) {
        $job->admin->notify(
            new NewJobApplicationNotification($job, auth('user')->user())
        );
    }

    return redirect()->route('user.jobs')
        ->with('success', 'Your application has been submitted successfully.');
}
   public function admin_applications(Request $request)
{
    $adminId = auth('admin')->id();
    $search = $request->search;

    $jobIds = Job::where('admin_id', $adminId)->pluck('id');

    $applications = JobApplication::with(['user', 'job','user.profile',]) 
        ->whereIn('job_id', $jobIds)
        ->when($search, function($query) use ($search) {

            $query->whereHas('user', function($userQ) use ($search) {
                $userQ->where('name', 'like', "%$search%");
            });

            $query->orWhere('resume', 'like', "%$search%");
        })
        ->orderBy('id', 'desc')
        ->paginate(5);

    return view('Admin.job_application', compact('applications'));
}

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,shortlisted,rejected,hired'
        ]);

        $application = JobApplication::with('job', 'user')->findOrFail($id);

        $adminId = auth('admin')->id();

        if ($application->job->admin_id != $adminId) {
            abort(403, "You are not allowed to update this application!");
        }

        $oldStatus = $application->status;
        $application->status = $request->status;
        $application->save();

        if ($oldStatus != $request->status) {
   Mail::to($application->user->email)
            ->queue(new ApplicationStatusMail(
                $application->user,
                $application->job,
                $application->status
            ));
           
        }
        $application->user->notify(
    new ApplicationStatusNotification(
        $application->job,
        $application->status
    )
);
 
        return back()->with('success', 'Application status updated successfully.');
    }

 
public function downloadResume($id)
{
    $application = JobApplication::with('job')->findOrFail($id);

    if (auth('admin')->id() !== $application->job->admin_id) {
        abort(403, 'Unauthorized');
    }

    if (!Storage::disk('public')->exists($application->resume)) {
        abort(404, 'Resume not found');
    }
    return Storage::disk('public')->download($application->resume);
}
public function applyFromSaved($id)
{
    $userId = Auth::guard('user')->id();

    // Delete saved job immediately
    SavedJob::where('user_id', $userId)
        ->where('job_id', $id)
        ->delete();

    // Redirect to apply form
    return redirect()->route('apply_form_job_application', $id);
}
}
