<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\JobApplication;
use App\Notifications\InterviewScheduledNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterviewController extends Controller
{
    // ─────────────────────────────────────────────
    // Schedule Form
    // ─────────────────────────────────────────────
    public function create($applicationId)
    {
        $application = JobApplication::with(['job', 'user', 'interview'])
            ->findOrFail($applicationId);

        // Only the job owner (admin) can access
        if ($application->job->admin_id !== auth('admin')->id()) {
            abort(403);
        }

        // Only shortlisted applications can be scheduled
        if ($application->status !== 'shortlisted') {
            return back()->with('error',
                'Interviews can only be scheduled for shortlisted applications.');
        }

        return view('Admin.interview_schedule', compact('application'));
    }

    // ─────────────────────────────────────────────
    // Store / Schedule Interview
    // ─────────────────────────────────────────────
    public function store(Request $request, $applicationId)
    {
        $application = JobApplication::with(['job', 'user'])
            ->findOrFail($applicationId);

        if ($application->job->admin_id !== auth('admin')->id()) {
            abort(403);
        }

        $data = $request->validate([
            'interview_date' => 'required|date|after_or_equal:today',
            'interview_time' => 'required',
            'mode' => 'required|in:online,offline',
            'location' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ], [
            'interview_date.after_or_equal' => 'Interview date must be today or a future date.',
            'mode.required' => 'Please select an interview mode (Online/Offline).',
        ]);

        $data['admin_id'] = auth('admin')->id();
        $data['status'] = 'scheduled';

        // Check if already exists (reschedule case)
        $isReschedule = $application->interview()->exists();

        // Conflict detection (Phase9 UX-03): warn if this admin already
        // has another interview at the exact same date/time. Not a hard
        // block — interview lengths vary and the admin may genuinely
        // intend to double-book (e.g. a panel), but they should see it.
        $conflict = Interview::where('admin_id', $data['admin_id'])
            ->where('interview_date', $data['interview_date'])
            ->where('interview_time', $data['interview_time'])
            ->where('job_application_id', '!=', $applicationId)
            ->where('status', '!=', 'cancelled')
            ->with('application.user')
            ->first();

        $interview = $application->interview()->updateOrCreate(
            ['job_application_id' => $applicationId],
            $data
        );

        if ($conflict) {
            $conflictWith = $conflict->application->user->name ?? 'another candidate';
            session()->flash('warning', "Heads up: you already have an interview scheduled with {$conflictWith} at this exact date and time.");
        }

        // If rescheduled, update status
        if ($isReschedule) {
            $interview->update(['status' => 'rescheduled']);
        }

        // Notify user
        $application->user->notify(
            new InterviewScheduledNotification(
                $interview->load('application.job.admin'),
                $isReschedule ? 'rescheduled' : 'scheduled'
            )
        );

        $message = $isReschedule
            ? 'Interview has been rescheduled successfully!'
            : 'Interview has been scheduled successfully!';

        return redirect()
            ->route('job_application')
            ->with('success', $message);
    }

    // ─────────────────────────────────────────────
    // Cancel Interview
    // ─────────────────────────────────────────────
    public function cancel($interviewId)
    {
        $interview = Interview::with('application.job.user')->findOrFail($interviewId);

        if ($interview->admin_id !== auth('admin')->id()) {
            abort(403);
        }

        $interview->update(['status' => 'cancelled']);

        // Notify user
        $interview->application->user->notify(
            new InterviewScheduledNotification($interview, 'cancelled')
        );

        return back()->with('success',
            'Interview has been cancelled and the user has been notified.');
    }

    // ─────────────────────────────────────────────
    // Mark Interview as Completed
    // ─────────────────────────────────────────────
    public function complete($interviewId)
    {
        $interview = Interview::findOrFail($interviewId);

        if ($interview->admin_id !== auth('admin')->id()) {
            abort(403);
        }

        $interview->update(['status' => 'completed']);

        return back()->with('success',
            'Interview has been marked as completed.');
    }

    // ─────────────────────────────────────────────
    // User — View Their Interviews
    // ─────────────────────────────────────────────
    public function userInterviews()
    {
        $userId = Auth::guard('user')->id();
        $today = now()->toDateString();

        $baseQuery = fn () => Interview::with(['application.job.admin'])
            ->whereHas('application', fn ($q) => $q->where('user_id', $userId));

        // Matches Interview::getIsUpcomingAttribute() (interview_date >= today)
        $upcoming = $baseQuery()
            ->where('interview_date', '>=', $today)
            ->orderBy('interview_date', 'asc')
            ->get();

        $past = $baseQuery()
            ->where('interview_date', '<', $today)
            ->orderBy('interview_date', 'desc')
            ->get();

        return view('User.my_interviews', [
            'upcoming' => $upcoming,
            'past' => $past,
        ]);
    }
}
