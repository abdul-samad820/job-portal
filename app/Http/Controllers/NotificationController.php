<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * A notification's "read" state and its target link were previously
     * disconnected — the dropdown link was a bare href="#" that did
     * nothing, and the only way to mark anything read was "mark all as
     * read" (Phase9 UX-05). This single route now does both: mark this
     * one notification read, then send the user to wherever it's about.
     */
    public function open(string $id)
    {
        // admin, superadmin, and user each use their own session-based
        // guard, so it's normal for more than one to be logged in at once
        // in the same browser (e.g. testing the admin panel in one tab
        // and the user panel in another). Picking the first authenticated
        // guard — regardless of who actually owns this notification —
        // meant an admin/superadmin session active anywhere in the
        // browser could shadow a user's own notifications and 404 them.
        // Instead, check every active guard and use whichever one
        // actually owns this notification.
        $guards = ['admin', 'superadmin', 'user'];
        $notification = null;
        $anyAuthenticated = false;

        foreach ($guards as $guard) {
            $notifiable = Auth::guard($guard)->user();

            if (! $notifiable) {
                continue;
            }

            $anyAuthenticated = true;

            $notification = $notifiable->notifications()->where('id', $id)->first();

            if ($notification) {
                break;
            }
        }

        if (! $anyAuthenticated) {
            abort(403);
        }

        if (! $notification) {
            abort(404);
        }

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect($notification->data['url'] ?? '/');
    }
}
