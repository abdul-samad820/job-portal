<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Welcomeemail;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{

    public function show_apply_job($id)
    {
        $job = Job::with('admin')->findOrFail($id);
        return view('User.user_job_application', compact('job'));
    }

   public function apply_job_application(Request $request, $id)
{
    $user_id = Auth::guard('user')->id(); 
    if (!$user_id) {
        return redirect()->route('user.login')->with('error', 'Please login first!');
    }

    $alreadyApplied = JobApplication::where('user_id', $user_id)
        ->where('job_id', $id)
        ->exists();

    if ($alreadyApplied) {
        return back()->with('error', 'You have already applied for this job!');
    }

    $data = $request->validate([
        'cover_letter' => 'nullable|string',
        'resume'       => 'required|mimes:pdf|max:2048',
    ]);

    $file = $request->file('resume');
    $filename = time() . '_' . $file->getClientOriginalName();
    $file->storeAs('resumes', $filename, 'public');
    $resumePath = 'storage/resumes/' . $filename;

    JobApplication::create([
        'user_id' => $user_id, 
        'job_id' => $id,
        'cover_letter' => $request->cover_letter,
        'resume' => $resumePath,
        'status' => 'pending',
    ]);

    return redirect()->route('user.jobs')->with('success', 'Your application has been submitted.');
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
        ->paginate(10);

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

            $email = $application->user->email;

            if ($request->status == 'hired') {
                $subject = "Congratulations! You are Hired!";
                $message = "Great news! You have been hired.";
            } elseif ($request->status == 'shortlisted') {
                $subject = "Application Shortlisted";
                $message = "Your application has been shortlisted.";
            } elseif ($request->status == 'rejected') {
                $subject = "Application Update";
                $message = "We regret to inform you that your application was not selected.";
            } else {
                $subject = "Application Status Updated";
                $message = "Your application status is now: " . ucfirst($request->status);
            }

            Mail::to($email)->send(new Welcomeemail($message, $subject));
        }

        return back()->with('success', 'Application status updated successfully.');
    }
}
