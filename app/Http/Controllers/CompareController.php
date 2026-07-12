<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request)
    {
        $jobIds = array_filter((array) $request->input('job_ids', []));
        $jobIds = array_slice($jobIds, 0, 3);

        $jobs = collect();
        if (! empty($jobIds)) {
            $jobs = Job::with(['admin', 'category', 'role'])
                ->visible()
                ->whereIn('id', $jobIds)
                ->get();
        }

        return view('User.job_compare', compact('jobs'));
    }
}
