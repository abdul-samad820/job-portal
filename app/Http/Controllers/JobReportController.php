<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\JobReport;
use App\Notifications\NewJobReportNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobReportController extends Controller
{
    public function store(Request $request, $jobId)
    {
        $data = $request->validate([
            'reason' => 'required|in:'.implode(',', array_keys(JobReport::REASONS)),
            'details' => 'nullable|string|max:1000',
        ]);

        $userId = Auth::guard('user')->id();

        // A user can only report a given job once — the unique DB
        // constraint on (job_id, user_id) backs this up too.
        $alreadyReported = JobReport::where('job_id', $jobId)
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyReported) {
            return back()->with('info', 'You have already reported this job. Our team will review it.');
        }

        $report = JobReport::create([
            'job_id' => $jobId,
            'user_id' => $userId,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'status' => JobReport::STATUS_PENDING,
        ]);

        // Alert every SuperAdmin so the reported-jobs queue doesn't sit
        // unnoticed — previously nothing surfaced this in the bell.
        Admin::where('role', 'super_admin')->get()->each(function ($superAdmin) use ($report) {
            $superAdmin->notify(new NewJobReportNotification($report));
        });

        return back()->with('success', 'Thanks for the report. Our team will review this job shortly.');
    }
}
