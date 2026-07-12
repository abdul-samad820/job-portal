<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Oversight of the platform's job-seeker user base. SuperAdmin can
 * search/view and — for abuse/fake accounts — suspend or reinstate a
 * user. Editing a user's actual profile data is still the user's own
 * responsibility via their own account settings.
 */
class SuperAdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status'); // active | suspended

        $users = User::withCount(['jobApplications', 'savedJobs'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($status === 'suspended', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('SuperAdmin.users', compact('users', 'search', 'status'));
    }

    public function suspend($id)
    {
        $user = User::findOrFail($id);
        $user->forceFill(['is_active' => false])->save();
        $user->tokens()->delete();

        ActivityLog::log('user_suspended', "Suspended user \"{$user->name}\" ({$user->email})", $user);

        return back()->with('success', "{$user->name} suspended successfully.");
    }

    public function unsuspend($id)
    {
        $user = User::findOrFail($id);
        $user->forceFill(['is_active' => true])->save();

        ActivityLog::log('user_unsuspended', "Reactivated user \"{$user->name}\" ({$user->email})", $user);

        return back()->with('success', "{$user->name} reactivated successfully.");
    }

    public function exportCsv(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $users = User::withCount(['jobApplications', 'savedJobs'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($status === 'suspended', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->get();

        $filename = 'users_'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Applications', 'Saved Jobs', 'Status', 'Joined']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone,
                    $user->job_applications_count,
                    $user->saved_jobs_count,
                    $user->is_active ? 'Active' : 'Suspended',
                    $user->created_at->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
