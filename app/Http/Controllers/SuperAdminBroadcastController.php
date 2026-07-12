<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\PlatformAnnouncementNotification;
use Illuminate\Http\Request;

class SuperAdminBroadcastController extends Controller
{
    public function create()
    {
        return view('SuperAdmin.broadcast');
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
            'audience' => 'required|in:admins,users,both',
            'send_email' => 'nullable|boolean',
        ]);

        $sendEmail = (bool) ($data['send_email'] ?? false);
        $notification = new PlatformAnnouncementNotification($data['title'], $data['message'], $sendEmail);

        $recipientCount = 0;

        // Chunked to keep memory flat regardless of how many admins/users
        // exist — same pattern already used in SendJobAlerts.
        if (in_array($data['audience'], ['admins', 'both'], true)) {
            Admin::where('role', 'admin')->chunk(200, function ($admins) use ($notification, &$recipientCount) {
                foreach ($admins as $admin) {
                    $admin->notify($notification);
                    $recipientCount++;
                }
            });
        }

        if (in_array($data['audience'], ['users', 'both'], true)) {
            User::chunk(200, function ($users) use ($notification, &$recipientCount) {
                foreach ($users as $user) {
                    $user->notify($notification);
                    $recipientCount++;
                }
            });
        }

        ActivityLog::log(
            'announcement_broadcast',
            "Broadcast \"{$data['title']}\" to {$recipientCount} recipient(s) ({$data['audience']})"
        );

        return back()->with('success', "Announcement sent to {$recipientCount} recipient(s).");
    }
}
