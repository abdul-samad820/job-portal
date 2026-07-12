<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Job;
use App\Models\JobReport;
use Illuminate\Http\Request;

class SuperAdminJobController extends Controller
{
    /**
     * All jobs across all companies — search/filter, with a quick view
     * of which ones are hidden or currently under report.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status'); // hidden | reported | expired | live | all

        $jobs = Job::with(['admin', 'category'])
            ->withCount('reports')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('admin', function ($a) use ($search) {
                            $a->where('company_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status === 'hidden', fn ($q) => $q->where('is_hidden', true))
            ->when($status === 'reported', fn ($q) => $q->has('reports'))
            ->when($status === 'expired', fn ($q) => $q->expired())
            ->when($status === 'live', fn ($q) => $q->live())
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('SuperAdmin.jobs', compact('jobs', 'search', 'status'));
    }

    public function hide(Request $request, $id)
    {
        $data = $request->validate([
            'hidden_reason' => 'required|string|max:500',
        ]);

        $job = Job::findOrFail($id);
        $job->update(['is_hidden' => true, 'hidden_reason' => $data['hidden_reason']]);

        ActivityLog::log('job_hidden', "Hid job \"{$job->title}\": {$data['hidden_reason']}", $job);

        if ($job->admin) {
            $job->admin->notify(new \App\Notifications\JobHiddenNotification($job, $data['hidden_reason']));
        }

        return back()->with('success', "\"{$job->title}\" has been hidden from public view.");
    }

    public function unhide($id)
    {
        $job = Job::findOrFail($id);
        $job->update(['is_hidden' => false, 'hidden_reason' => null]);

        ActivityLog::log('job_unhidden', "Unhid job \"{$job->title}\"", $job);

        return back()->with('success', "\"{$job->title}\" is now visible again.");
    }

    public function destroy($id)
    {
        $job = Job::findOrFail($id);

        $applicationCount = $job->applications()->count();
        if ($applicationCount > 0 && ! request()->has('confirm_delete')) {
            return back()->with('error', "This job has {$applicationCount} application(s). Add ?confirm_delete=1 to confirm permanent deletion.");
        }

        $title = $job->title;
        $jobId = $job->id;
        $job->delete();

        ActivityLog::create([
            'superadmin_id' => auth('superadmin')->id(),
            'action' => 'job_deleted',
            'subject_type' => 'Job',
            'subject_id' => $jobId,
            'description' => "Deleted job \"{$title}\"",
        ]);

        return back()->with('success', "\"{$title}\" permanently deleted.");
    }

    /**
     * User-submitted reports queue — grouped implicitly by job via the
     * job relation, newest first, pending ones surfaced first.
     */
    public function reports(Request $request)
    {
        $filter = $request->query('filter', 'pending');

        $reports = JobReport::with(['job.admin', 'user'])
            ->when(in_array($filter, ['pending', 'reviewed', 'dismissed'], true), function ($q) use ($filter) {
                $q->where('status', $filter);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $pendingCount = JobReport::pending()->count();

        return view('SuperAdmin.job_reports', compact('reports', 'filter', 'pendingCount'));
    }

    public function dismissReport($id)
    {
        JobReport::findOrFail($id)->update(['status' => JobReport::STATUS_DISMISSED]);

        return back()->with('success', 'Report dismissed.');
    }

    /**
     * Mark a report reviewed AND hide the underlying job in one action —
     * the common path when a report turns out to be legitimate.
     */
    public function actionReport(Request $request, $id)
    {
        $data = $request->validate([
            'hidden_reason' => 'required|string|max:500',
        ]);

        $report = JobReport::findOrFail($id);
        $report->job->update(['is_hidden' => true, 'hidden_reason' => $data['hidden_reason']]);
        $report->update(['status' => JobReport::STATUS_REVIEWED]);

        if ($report->job->admin) {
            $report->job->admin->notify(new \App\Notifications\JobHiddenNotification($report->job, $data['hidden_reason']));
        }

        // Any other pending reports on the same job are now moot.
        JobReport::where('job_id', $report->job_id)
            ->where('status', JobReport::STATUS_PENDING)
            ->update(['status' => JobReport::STATUS_REVIEWED]);

        return back()->with('success', 'Job hidden and report(s) marked reviewed.');
    }
}
