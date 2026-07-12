<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationStatusMail;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\SavedJob;
use App\Notifications\ApplicationStatusNotification;
use App\Notifications\NewJobApplicationNotification;
use App\Traits\VerifiesUploadedFileMime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    use VerifiesUploadedFileMime;

    public function show_apply_job($id)
    {
        $job = Job::with('admin')->findOrFail($id);
        $alreadyApplied = JobApplication::where('user_id', Auth::guard('user')->id())
            ->where('job_id', $id)->exists();
        $resumes = \App\Models\Resume::where('user_id', Auth::guard('user')->id())->latest()->get();

        return view('User.user_job_application', compact('job', 'alreadyApplied', 'resumes'));
    }

    public function apply(Request $request, $id)
    {
        $user_id = Auth::guard('user')->id();
        $job = Job::with('admin')->findOrFail($id);
        // Deadline Check
        if ($job->last_date && now()->greaterThan($job->last_date)) {
            return back()->with('error', 'Application deadline has passed.');
        }
        // Profile Check
        $profile = auth('user')->user()->profile;
        if (
            ! $profile || empty($profile->professional_summary) || empty($profile->core_skills) ||
            empty($profile->education)
        ) {
            return redirect()->route('user.profile')
                ->with('error', 'Please complete your profile before applying.');
        }
        // Already Applied Check
        $alreadyApplied = JobApplication::where('user_id', $user_id)
            ->where('job_id', $id)->first();

        if ($alreadyApplied) {
            return back()->with('error', 'You have already applied for this job.');
        }
        // Validation
        $request->validate([
            'cover_letter' => 'required|string|min:20|max:500',
            'resume_source' => 'required|in:library,upload',
            'resume_id' => 'required_if:resume_source,library|nullable|exists:resumes,id',
            'resume' => 'required_if:resume_source,upload|nullable|mimes:pdf|max:2048',
        ]);

        $resumeId = null;

        if ($request->resume_source === 'library') {
            // Re-use a resume already saved in the user's library — no
            // re-upload needed. We still copy the path onto the
            // application as a snapshot, so it keeps working even if the
            // library entry is later deleted.
            $libraryResume = \App\Models\Resume::where('user_id', $user_id)
                ->where('id', $request->resume_id)
                ->first();

            if (! $libraryResume) {
                return back()->with('error', 'Selected resume was not found in your library.');
            }

            $path = $libraryResume->file_path;
            $resumeId = $libraryResume->id;
        } else {
            $path = $request->file('resume')->store('resumes', 'public');
            if (! $this->verifyStoredMime('public', $path, ['application/pdf'])) {
                return back()->with('error', 'Invalid file type. Please upload a valid PDF.');
            }

            // Optionally save this freshly uploaded file into the resume
            // library too, so the user can reuse it next time.
            if ($request->boolean('save_to_library')) {
                $count = \App\Models\Resume::where('user_id', $user_id)->count();
                if ($count < 5) {
                    $newLibraryResume = \App\Models\Resume::create([
                        'user_id' => $user_id,
                        'title' => $request->input('resume_title', 'Resume - '.now()->format('d M Y')),
                        'file_path' => $path,
                        'original_name' => $request->file('resume')->getClientOriginalName(),
                        'file_size' => $request->file('resume')->getSize(),
                        'is_default' => $count === 0,
                    ]);
                    $resumeId = $newLibraryResume->id;
                }
            }
        }

        $application = JobApplication::create([
            'user_id' => $user_id,
            'job_id' => $id,
            'cover_letter' => $request->cover_letter,
            'resume' => $path,
            'resume_id' => $resumeId,
            'status' => 'pending',
        ]);

        \App\Models\ApplicationStatusHistory::create([
            'job_application_id' => $application->id,
            'from_status' => null,
            'to_status' => 'pending',
        ]);

        \App\Models\JobInvite::where('job_id', $id)->where('user_id', $user_id)->update(['status' => 'applied']);

        // SEND NOTIFICATION (AFTER SUCCESS)
        if ($job->admin) {
            $job->admin->notify(
                new NewJobApplicationNotification($job, auth('user')->user())
            );
        }

        // Remove from saved jobs now that the application actually succeeded
        SavedJob::where('user_id', $user_id)->where('job_id', $id)->delete();

        return redirect()->route('user.jobs')
            ->with('success', 'Your application has been submitted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,shortlisted,rejected,hired',
        ]);

        $application = JobApplication::with('job', 'user')->findOrFail($id);
        $adminId = auth('admin')->id();
        if ($application->job->admin_id != $adminId) {
            abort(403, 'You are not allowed to update this application!');
        }
        $oldStatus = $application->status;
        $application->status = $request->status;
        $application->status_updated_at = now();
        $application->updated_by_admin_id = $adminId;
        $application->save();

        if ($oldStatus != $request->status) {
            $this->notifyStatusChange($application);

            \App\Models\ApplicationStatusHistory::create([
                'job_application_id' => $application->id,
                'from_status' => $oldStatus,
                'to_status' => $request->status,
                'changed_by_admin_id' => $adminId,
            ]);

            \App\Models\ActivityLog::logAdmin(
                'application_status_updated',
                "Changed {$application->user->name}'s application for \"{$application->job->title}\" from {$oldStatus} to {$request->status}",
                $application
            );
        }

        return back()->with('success', 'Application status updated successfully.');
    }

    /**
     * Update the status of many applications in one go — reviewing
     * applications one-by-one doesn't scale once a job gets more than a
     * handful of applicants. Still enforces per-application ownership
     * so an admin can't touch another company's applications by
     * tampering with the submitted IDs.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $data = $request->validate([
            'application_ids' => 'required|array|min:1',
            'application_ids.*' => 'integer',
            'status' => 'required|in:pending,shortlisted,rejected,hired',
        ]);

        $adminId = auth('admin')->id();

        $applications = JobApplication::with('job', 'user')
            ->whereIn('id', $data['application_ids'])
            ->whereHas('job', fn ($q) => $q->where('admin_id', $adminId))
            ->get();

        $updated = 0;

        foreach ($applications as $application) {
            $oldStatus = $application->status;

            if ($oldStatus === $data['status']) {
                continue;
            }

            $application->status = $data['status'];
            $application->status_updated_at = now();
            $application->updated_by_admin_id = $adminId;
            $application->save();

            \App\Models\ApplicationStatusHistory::create([
                'job_application_id' => $application->id,
                'from_status' => $oldStatus,
                'to_status' => $data['status'],
                'changed_by_admin_id' => $adminId,
            ]);

            $this->notifyStatusChange($application);
            $updated++;
        }

        if ($updated > 0) {
            \App\Models\ActivityLog::logAdmin(
                'application_status_updated',
                "Bulk-updated {$updated} application(s) to \"{$data['status']}\"",
            );
        }

        return back()->with('success', "{$updated} application(s) updated to \"{$data['status']}\".");
    }

    /**
     * Shared by both the single and bulk status-change paths so the
     * applicant always gets the same email + in-app notification
     * regardless of which flow the admin used.
     */
    private function notifyStatusChange(JobApplication $application): void
    {
        Mail::to($application->user->email)
            ->queue(new ApplicationStatusMail(
                $application->user,
                $application->job,
                $application->status
            ));

        $application->user->notify(
            new ApplicationStatusNotification(
                $application->job, $application->status
            )
        );
    }

    public function downloadResume($id)
    {
        $application = JobApplication::with('job')->findOrFail($id);
        if (auth('admin')->id() !== $application->job->admin_id) {
            abort(403, 'Unauthorized');
        }
        if (! Storage::disk('public')->exists($application->resume)) {
            abort(404, 'Resume not found');
        }

        return Storage::disk('public')->download($application->resume);
    }

    public function applyFromSaved($id)
    {
        // Just take the user to the apply form. The saved-job record is only
        // removed once the application actually succeeds (see apply()).
        return redirect()->route('apply_form_job_application', $id);
    }

    public function updateNote(Request $request, $id)
    {
        $application = JobApplication::with('job')->findOrFail($id);
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        if ($application->job->admin_id != auth('admin')->id()) {
            abort(403);
        }

        $application->admin_note = $request->admin_note;
        $application->save();

        return back()->with('success', 'Note saved');
    }

    public function jobapplications(Request $request)
    {
        $adminId = auth('admin')->id();
        $jobIds = Job::where('admin_id', $adminId)->pluck('id');
        $applications = JobApplication::with(['user.profile', 'job.category', 'updatedBy', 'interview'])
            ->whereIn('job_id', $jobIds)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $applications->getCollection()->transform(function ($app) {
            $jobSkills = $app->job->required_skills ?? '';
            $userSkills = $app->user->profile->core_skills ?? '';
            $job = array_map('trim', explode(',', strtolower($jobSkills)));
            $user = array_map('trim', explode(',', strtolower($userSkills)));
            $matched = count(array_intersect($job, $user));
            $total = count(array_filter($job));
            $app->match_percentage = $total > 0
                ? round(($matched / $total) * 100) : 0;

            return $app;
        });

        return view('Admin.job_application', compact('applications'));
    }

    /**
     * Withdraw a pending application (web/user-facing version).
     * Only the owning user can withdraw, and only while the application
     * is still pending — once an admin has acted on it (shortlisted /
     * hired / rejected), withdrawing no longer makes sense.
     */
    public function withdraw($id)
    {
        $userId = Auth::guard('user')->id();

        $application = JobApplication::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (! $application) {
            return back()->with('error', 'Application not found.');
        }

        if ($application->status !== 'pending') {
            return back()->with('error',
                'Only pending applications can be withdrawn. This application is already '.$application->status.'.');
        }

        if ($application->resume) {
            Storage::disk('public')->delete($application->resume);
        }

        $application->delete();

        return back()->with('success', 'Application withdrawn successfully.');
    }

    /**
     * Show the full status history ("timeline") of one of the logged-in
     * user's own applications — e.g. applied on X, shortlisted on Y,
     * hired on Z — instead of just the current status.
     */
    public function timeline($id)
    {
        $userId = Auth::guard('user')->id();
        $application = JobApplication::with(['job', 'statusHistory'])
            ->where('user_id', $userId)
            ->findOrFail($id);

        return view('User.application_timeline', compact('application'));
    }

    public function viewResume($id)
    {
        $application = JobApplication::with('job')->findOrFail($id);

        if (auth('admin')->id() !== $application->job->admin_id) {
            abort(403, 'Unauthorized');
        }

        if (! Storage::disk('public')->exists($application->resume)) {
            abort(404, 'Resume not found');
        }

        return Storage::disk('public')->response($application->resume);
    }
}
