<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CSV exports for the company admin dashboard's "Export" quick action.
 * Every export here is scoped to the logged-in admin's own data only —
 * unlike SuperAdminExportController, which exports platform-wide data.
 */
class AdminExportController extends Controller
{
    public function jobs(): StreamedResponse
    {
        $adminId = Auth::guard('admin')->id();

        $jobs = Job::with(['category', 'role'])
            ->withCount('applications')
            ->where('admin_id', $adminId)
            ->latest()
            ->get();

        return $this->streamCsv('jobs-export-'.now()->format('Y-m-d').'.csv',
            ['ID', 'Title', 'Category', 'Role', 'Location', 'Type', 'Min Salary', 'Max Salary', 'Status', 'Applications', 'Last Date', 'Posted On'],
            $jobs,
            fn ($j) => [
                $j->id,
                $j->title,
                $j->category->name ?? '—',
                $j->role->name ?? '—',
                $j->location,
                $j->type,
                $j->min_salary ? round($j->min_salary / 100000, 1).' LPA' : '—',
                $j->max_salary ? round($j->max_salary / 100000, 1).' LPA' : '—',
                $j->is_hidden ? 'Hidden' : 'Live',
                $j->applications_count,
                optional($j->last_date)->format('Y-m-d'),
                $j->created_at->format('Y-m-d'),
            ]
        );
    }

    public function applications(): StreamedResponse
    {
        $adminId = Auth::guard('admin')->id();
        $jobIds = Job::where('admin_id', $adminId)->pluck('id');

        $applications = JobApplication::with(['user', 'job'])
            ->whereIn('job_id', $jobIds)
            ->latest()
            ->get();

        return $this->streamCsv('applications-export-'.now()->format('Y-m-d').'.csv',
            ['ID', 'Candidate Name', 'Email', 'Job Title', 'Status', 'Expected Salary', 'Notice Period', 'Applied On'],
            $applications,
            fn ($a) => [
                $a->id,
                $a->user->name ?? 'N/A',
                $a->user->email ?? 'N/A',
                $a->job->title ?? 'N/A',
                ucfirst($a->status),
                $a->expected_salary ? round($a->expected_salary / 100000, 1).' LPA' : '—',
                $a->notice_period ?? '—',
                $a->created_at->format('Y-m-d'),
            ]
        );
    }

    public function candidates(): StreamedResponse
    {
        $adminId = Auth::guard('admin')->id();
        $jobIds = Job::where('admin_id', $adminId)->pluck('id');

        // One row per unique candidate who has applied to any of this
        // admin's jobs, with their application count and most recent status.
        $candidates = JobApplication::with(['user', 'job'])
            ->whereIn('job_id', $jobIds)
            ->latest()
            ->get()
            ->unique('user_id')
            ->map(function ($application) use ($jobIds) {
                $application->applications_count = JobApplication::where('user_id', $application->user_id)
                    ->whereIn('job_id', $jobIds)
                    ->count();

                return $application;
            });

        return $this->streamCsv('candidates-export-'.now()->format('Y-m-d').'.csv',
            ['Candidate Name', 'Email', 'Phone', 'Applications With You', 'Latest Job Applied', 'Latest Status'],
            $candidates,
            fn ($a) => [
                $a->user->name ?? 'N/A',
                $a->user->email ?? 'N/A',
                $a->user->phone ?? '—',
                $a->applications_count,
                $a->job->title ?? 'N/A',
                ucfirst($a->status),
            ]
        );
    }

    private function streamCsv(string $filename, array $header, $rows, callable $rowMapper): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows, $rowMapper) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);

            foreach ($rows as $row) {
                fputcsv($out, array_map([$this, 'sanitizeCsvCell'], $rowMapper($row)));
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function sanitizeCsvCell($value)
    {
        if (is_string($value) && preg_match('/^[=+\-@]/', $value)) {
            return "'".$value;
        }

        return $value;
    }
}
