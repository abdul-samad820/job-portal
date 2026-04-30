<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\SavedJob;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobApiController extends Controller
{
    use ApiResponse;

    // ─────────────────────────────────────
    // All Jobs — with filters
    // ─────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $query = Job::with(['category', 'role', 'admin'])
            ->whereDate('last_date', '>=', now()); // Expired jobs

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('type')) {
            $query->whereIn('type', (array) $request->type);
        }

        if ($request->filled('experience')) {
            $query->whereIn('experience', (array) $request->experience);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Salary range
        if ($request->filled('min_salary')) {
            $query->where('min_salary', '>=', $request->min_salary);
        }

        if ($request->filled('max_salary')) {
            $query->where('max_salary', '<=', $request->max_salary);
        }

        // Sort
        match ($request->sort) {
            'salary_high' => $query->orderBy('max_salary', 'desc'),
            'salary_low' => $query->orderBy('min_salary', 'asc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $jobs = $query->paginate($request->get('per_page', 10));

        // Response format
        $formattedJobs = collect($jobs->items())->map(fn ($job) => [
            'id' => $job->id,
            'title' => $job->title,
            'company' => $job->admin->company_name ?? null,
            'location' => $job->location,
            'type' => $job->type,
            'experience' => $job->experience,
            'min_salary' => $job->min_salary,
            'max_salary' => $job->max_salary,
            'salary_range' => $job->min_salary
                              ? '₹'.number_format($job->min_salary).
                                ' - ₹'.number_format($job->max_salary)
                              : 'Not disclosed',
            'category' => $job->category->name ?? null,
            'role' => $job->role->name ?? null,
            'last_date' => $job->last_date?->format('d M Y'),
            'posted_at' => $job->created_at->diffForHumans(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jobs fetched successfully',
            'data' => $formattedJobs,
            'meta' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
            ],
        ]);
    }

    // ─────────────────────────────────────
    // Single Job Detail
    // ─────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $job = Job::with(['category', 'role', 'admin'])->find($id);

        if (! $job) {
            return $this->error('Job not found.', 404);
        }

        return $this->success([
            'id' => $job->id,
            'title' => $job->title,
            'description' => $job->description,
            'overview' => $job->overview,
            'responsibilities' => $job->responsibilities,
            'required_skills' => $job->required_skills,
            'company' => $job->admin->company_name ?? null,
            'location' => $job->location,
            'type' => $job->type,
            'experience' => $job->experience,
            'min_salary' => $job->min_salary,
            'max_salary' => $job->max_salary,
            'salary_range' => $job->min_salary
                                  ? '₹'.number_format($job->min_salary).
                                    ' - ₹'.number_format($job->max_salary)
                                  : 'Not disclosed',
            'category' => $job->category->name ?? null,
            'role' => $job->role->name ?? null,
            'last_date' => $job->last_date?->format('d M Y'),
            'is_expired' => $job->last_date
                                  ? now()->greaterThan($job->last_date)
                                  : false,
            'posted_at' => $job->created_at->diffForHumans(),
        ], 'Job fetched successfully.');
    }

    // ─────────────────────────────────────
    // Save Job
    // ─────────────────────────────────────
    public function saveJob(Request $request, int $id): JsonResponse
    {
        $job = Job::find($id);

        if (! $job) {
            return $this->error('Job not found.', 404);
        }

        $userId = $request->user()->id;
        $already = SavedJob::where('user_id', $userId)
            ->where('job_id', $id)->exists();

        if ($already) {
            return $this->error('Job already saved.', 409);
        }

        SavedJob::create([
            'user_id' => $userId,
            'job_id' => $id,
        ]);

        return $this->success(null, '✅ Job saved successfully!', 201);
    }

    // ─────────────────────────────────────
    // Unsave Job
    // ─────────────────────────────────────
    public function unsaveJob(Request $request, int $id): JsonResponse
    {
        $deleted = SavedJob::where('user_id', $request->user()->id)
            ->where('job_id', $id)
            ->delete();

        if (! $deleted) {
            return $this->error('Saved job not found.', 404);
        }

        return $this->success(null, '🗑️ Job removed from saved list.');
    }

    // ─────────────────────────────────────
    // Saved Jobs List
    // ─────────────────────────────────────
    public function savedJobs(Request $request): JsonResponse
    {
        $savedJobs = SavedJob::with('job.admin')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        $formatted = collect($savedJobs->items())->map(fn ($saved) => [
            'saved_at' => $saved->created_at->diffForHumans(),
            'job' => [
                'id' => $saved->job->id,
                'title' => $saved->job->title,
                'company' => $saved->job->admin->company_name ?? null,
                'location' => $saved->job->location,
                'type' => $saved->job->type,
            ],
        ]);

        return $this->paginated(
            $savedJobs,
            'Saved jobs fetched successfully.'
        );
    }
}
