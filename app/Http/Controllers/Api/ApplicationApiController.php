<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplicationApiController extends Controller
{
    use ApiResponse;

    // ─────────────────────────────────────
    // My Applications
    // ─────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $applications = JobApplication::with(['job.admin'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        $formatted = collect($applications->items())->map(fn ($app) => [
            'id' => $app->id,
            'status' => $app->status,
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

        return response()->json([
            'status' => 'success',
            'message' => 'Applications fetched successfully.',
            'data' => $formatted,
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'total' => $applications->total(),
            ],
        ]);
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

        // Already applied check
        $alreadyApplied = JobApplication::where('user_id', $user->id)
            ->where('job_id', $id)
            ->where('status', '!=', 'rejected')
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

        // Resume store karo
        $path = $request->file('resume')->store('resumes', 'public');

        $application = JobApplication::create([
            'user_id' => $user->id,
            'job_id' => $id,
            'cover_letter' => $request->cover_letter,
            'resume' => $path,
            'status' => 'pending',
        ]);

        // Admin ko notify karo
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
 
}
