<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStatusHistory;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Resume;
use App\Models\SavedJob;
use App\Notifications\NewJobApplicationNotification;
use App\Traits\VerifiesUploadedFileMime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BulkApplyController extends Controller
{
    use VerifiesUploadedFileMime;

    /**
     * Review screen — shows the jobs the user checked on the listing
     * page, and lets them pick one resume + write one cover letter that
     * gets used for every one of them.
     */
    public function form(Request $request)
    {
        $userId = Auth::guard('user')->id();
        $user = Auth::guard('user')->user();

        $jobIds = array_filter((array) $request->input('job_ids', []));
        if (empty($jobIds)) {
            return redirect()->route('user.jobs')->with('error', 'No jobs were selected.');
        }

        $limit = \App\Services\BadgeLimitService::limitFor($user, 'bulk_apply_jobs');
        if ($limit !== null && count($jobIds) > $limit) {
            return redirect()->route('user.jobs')->with('error',
                "You can bulk apply to up to {$limit} jobs at once. Refer more friends to raise this limit.");
        }

        $alreadyAppliedIds = JobApplication::where('user_id', $userId)
            ->whereIn('job_id', $jobIds)
            ->pluck('job_id')
            ->toArray();

        $jobs = Job::with('admin')
            ->visible()
            ->whereIn('id', $jobIds)
            ->get()
            ->filter(fn ($job) => ! $job->is_expired && ! in_array($job->id, $alreadyAppliedIds));

        if ($jobs->isEmpty()) {
            return redirect()->route('user.jobs')
                ->with('error', 'None of the selected jobs are available to apply to (already applied or expired).');
        }

        $resumes = Resume::where('user_id', $userId)->latest()->get();

        return view('User.bulk_apply', compact('jobs', 'resumes'));
    }

    /**
     * Apply the same cover letter + resume to every selected job in one
     * submission. Each job still goes through the normal checks
     * (deadline, duplicate, profile completeness) individually, so a
     * problem with one job never blocks the rest.
     */
    public function store(Request $request)
    {
        $userId = Auth::guard('user')->id();
        $user = Auth::guard('user')->user();

        $profile = $user->profile;
        if (! $profile || empty($profile->professional_summary) || empty($profile->core_skills) || empty($profile->education)) {
            return redirect()->route('user.profile')
                ->with('error', 'Please complete your profile before applying.');
        }

        $request->validate([
            'job_ids' => 'required|array|min:1',
            'job_ids.*' => 'integer',
            'cover_letter' => 'required|string|min:20|max:500',
            'resume_source' => 'required|in:library,upload',
            'resume_id' => 'required_if:resume_source,library|nullable|exists:resumes,id',
            'resume' => 'required_if:resume_source,upload|nullable|mimes:pdf|max:2048',
        ]);

        $limit = \App\Services\BadgeLimitService::limitFor($user, 'bulk_apply_jobs');
        if ($limit !== null && count($request->job_ids) > $limit) {
            return back()->with('error',
                "You can bulk apply to up to {$limit} jobs at once. Refer more friends to raise this limit.");
        }

        // Resolve the resume file path ONCE — every selected job reuses it.
        if ($request->resume_source === 'library') {
            $libraryResume = Resume::where('user_id', $userId)->where('id', $request->resume_id)->first();
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
            $resumeId = null;
        }

        $applied = 0;
        $skipped = 0;

        foreach ($request->job_ids as $jobId) {
            $job = Job::find($jobId);

            if (! $job || $job->is_hidden) {
                $skipped++;

                continue;
            }

            if ($job->last_date && now()->greaterThan($job->last_date)) {
                $skipped++;

                continue;
            }

            $alreadyApplied = JobApplication::where('user_id', $userId)->where('job_id', $jobId)->exists();
            if ($alreadyApplied) {
                $skipped++;

                continue;
            }

            $application = JobApplication::create([
                'user_id' => $userId,
                'job_id' => $jobId,
                'cover_letter' => $request->cover_letter,
                'resume' => $path,
                'resume_id' => $resumeId,
                'status' => 'pending',
            ]);

            ApplicationStatusHistory::create([
                'job_application_id' => $application->id,
                'from_status' => null,
                'to_status' => 'pending',
            ]);

            if ($job->admin) {
                $job->admin->notify(new NewJobApplicationNotification($job, $user));
            }

            SavedJob::where('user_id', $userId)->where('job_id', $jobId)->delete();
            $applied++;
        }

        $message = "Applied to {$applied} job(s) successfully.";
        if ($skipped > 0) {
            $message .= " {$skipped} job(s) were skipped (already applied, expired, or no longer available).";
        }

        return redirect()->route('user.job_applied')->with('success', $message);
    }
}
