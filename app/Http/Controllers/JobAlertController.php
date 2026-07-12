<?php

namespace App\Http\Controllers;

use App\Models\JobAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobAlertController extends Controller
{
    public function index()
    {
        $alert = JobAlert::where('user_id', Auth::guard('user')->id())->first();

        return view('User.job_alert', compact('alert'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'keywords' => 'required|string|max:255',
        ], [
            'keywords.required' => 'Please enter at least one keyword..',
        ]);

        // Clean keywords
        $keywords = implode(', ', array_map(
            'trim',
            explode(',', $request->keywords)
        ));

        // Update ya Create (upsert)
        JobAlert::updateOrCreate(
            ['user_id' => Auth::guard('user')->id()],
            [
                'keywords' => $keywords,
                'is_active' => true,
            ]
        );

        return back()->with('success', ' Job alert saved successfully!');
    }

    public function toggle()
    {
        $alert = JobAlert::where('user_id', Auth::guard('user')->id())
            ->firstOrFail();

        $alert->update(['is_active' => ! $alert->is_active]);

        $status = $alert->is_active ? 'activated' : 'deactivated';

        return back()->with('success', " Job alert {$status}!");
    }

    public function destroy()
    {
        JobAlert::where('user_id', Auth::guard('user')->id())->delete();

        return back()->with('success', ' The job alert has been removed.');
    }
}
