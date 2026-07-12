<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobInvite;
use App\Models\User;
use App\Notifications\JobInviteNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidateController extends Controller
{
    /**
     * Browse job seekers who've marked themselves "Open to Work" —
     * the pool a recruiter can proactively invite to apply.
     */
    public function index(Request $request)
    {
        $adminId = Auth::guard('admin')->id();

        $query = User::whereHas('profile', function ($q) {
            $q->where('open_to_work', true);
        })->with('profile');

        if ($request->filled('skill')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('core_skills', 'like', '%'.$request->skill.'%');
            });
        }

        $candidates = $query->paginate(12)->withQueryString();
        $myJobs = Job::where('admin_id', $adminId)->visible()->orderByDesc('id')->get();

        return view('Admin.candidates', compact('candidates', 'myJobs'));
    }

    /**
     * Invite a candidate to apply for one of this admin's own jobs.
     */
    public function invite(Request $request, $userId)
    {
        $adminId = Auth::guard('admin')->id();

        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'message' => 'nullable|string|max:500',
        ]);

        $job = Job::where('id', $request->job_id)->where('admin_id', $adminId)->first();
        if (! $job) {
            return back()->with('error', 'You can only invite candidates to your own job postings.');
        }

        $existing = JobInvite::where('job_id', $job->id)->where('user_id', $userId)->first();
        if ($existing) {
            return back()->with('error', 'You already invited this candidate to this job.');
        }

        $invite = JobInvite::create([
            'job_id' => $job->id,
            'user_id' => $userId,
            'admin_id' => $adminId,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        $user = User::find($userId);
        if ($user) {
            $user->notify(new JobInviteNotification($invite));
        }

        return back()->with('success', 'Invitation sent successfully.');
    }
}
