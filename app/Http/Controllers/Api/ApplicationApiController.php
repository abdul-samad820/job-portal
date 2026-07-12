<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Traits\ApiResponse;
use App\Traits\VerifiesUploadedFileMime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplicationApiController extends Controller
{
    use ApiResponse, VerifiesUploadedFileMime;

    // ─────────────────────────────────────
    // My Applications
    // ─────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $query = JobApplication::with(['job.admin', 'interview'])
            ->where('user_id', $request->user()->id);

        // Optional status filter, e.g. GET /applications?status=pending
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(10);

        $formatted = collect($applications->items())->map(fn ($app) => [
            'id' => $app->id,
            'status' => $app->status,
            'status_color' => $app->statusColor(),
            'applied_at' => $app->created_at->diffForHumans(),
            'cover_letter' => $app->cover_letter,
            'job' => [
                'id' => $app->job->id,
                'title' => $app->job->title,
                'company' => $app->job->admin->company_name ?? null,
                'location' => $app->job->location,
                'type' => $app->job->type,
            ],
            'interview' => $app->interview ? [
                'date' => $app->interview->formatted_date_time,
                'mode' => $app->interview->mode,
                'location' => $app->interview->location,
                'status' => $app->interview->status,
            ] : null,
        ]);

        return $this->paginated($applications, 'Applications fetched successfully.', $formatted);
    }

    // ─────────────────────────────────────
    // Apply for Job
    // ─────────────────────────────────────
    public function apply(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $job = Job::with('admin')->find($id);

        if (! $job) {
            return $this->error('Job not found.', 404);
        }

        // Deadline check
        if ($job->last_date && now()->greaterThan($job->last_date)) {
            return $this->error('Application deadline has passed.', 422);
        }

        // Already applied check — matches the DB-level unique constraint
        // on (user_id, job_id), which blocks a second row regardless of
        // status. An "exclude rejected" check here would just crash with
        // a DB error the moment a rejected user tried to re-apply.
        $alreadyApplied = JobApplication::where('user_id', $user->id)
            ->where('job_id', $id)
            ->exists();

        if ($alreadyApplied) {
            return $this->error('You have already applied for this job.', 409);
        }

        // Profile check
        $profile = $user->profile;

        if (! $profile ||
            empty($profile->professional_summary) ||
            empty($profile->core_skills) ||
            empty($profile->education)) {
            return $this->error(
                'Please complete your profile before applying.',
                422,
                ['profile' => 'Professional summary, skills, and education are required.']
            );
        }

        // Validate
        try {
            $request->validate([
                'cover_letter' => 'required|string|min:20|max:500',
                'resume' => 'required|mimes:pdf|max:2048',
            ]);
        } catch (ValidationException $e) {
            return $this->error('Validation failed.', 422, $e->errors());
        }

        // Store resume
        $path = $request->file('resume')->store('resumes', 'public');

        if (! $this->verifyStoredMime('public', $path, ['application/pdf'])) {
            return $this->error('Invalid file type. Please upload a valid PDF.', 422);
        }

        // Wrapped in try/catch: the "already applied" check above has a
        // race-condition window (two near-simultaneous requests can both
        // pass it). The DB-level unique constraint on (user_id, job_id)
        // is the real guard — this just turns that constraint violation
        // into a clean 409 instead of a raw 500.
        try {
            $application = JobApplication::create([
                'user_id' => $user->id,
                'job_id' => $id,
                'cover_letter' => $request->cover_letter,
                'resume' => $path,
                'status' => 'pending',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ((int) $e->getCode() === 23000) {
                return $this->error('You have already applied for this job.', 409);
            }

            throw $e;
        }

        // Notify admin
        if ($job->admin) {
            $job->admin->notify(
                new \App\Notifications\NewJobApplicationNotification($job, $user)
            );
        }

        return $this->success([
            'application_id' => $application->id,
            'status' => 'pending',
            'job_title' => $job->title,
        ], 'Application submitted successfully!', 201);
    }

    // ─────────────────────────────────────
    // Withdraw Application
    // ─────────────────────────────────────
    public function withdraw(Request $request, int $id): JsonResponse
    {
        $application = JobApplication::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $application) {
            return $this->error('Application not found.', 404);
        }

        if ($application->status !== 'pending') {
            return $this->error(
                'Only pending applications can be withdrawn. This application is already '.$application->status.'.',
                422
            );
        }

        if ($application->resume) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($application->resume);
        }

        $application->delete();

        return $this->success(null, 'Application withdrawn successfully.');
    }
}
