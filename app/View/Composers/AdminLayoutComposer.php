<?php

namespace App\View\Composers;

use App\Models\Testimonial;
use Illuminate\View\View;

class AdminLayoutComposer
{
    /**
     * Bind data to the Admin layout view.
     *
     * Replaces the @php block that was previously firing DB queries
     * directly inside resources/views/layouts/Admin_layout.blade.php.
     *
     * Variables provided to the layout:
     *   $adminUser                  — the authenticated Admin model instance
     *   $notifications              — Collection of up to 5 latest unread notifications
     *   $unreadCount                — integer total count of all unread notifications
     *   $pendingTestimonialsCount   — integer count of testimonials awaiting moderation
     */
    public function compose(View $view): void
    {
        if (! auth('admin')->check()) {
            // Guest context — supply safe empty defaults so the layout
            // never throws "trying to get property of non-object".
            $view->with([
                'adminUser' => null,
                'notifications' => collect(),
                'unreadCount' => 0,
                'pendingTestimonialsCount' => 0,
            ]);

            return;
        }

        $admin = auth('admin')->user();

        // Single query: fetch only the 5 most recent unread rows,
        // not ALL unread rows (previously loaded the full collection
        // then sliced in PHP — wasteful for admins with many notifications).
        $notifications = $admin->unreadNotifications()
            ->latest()
            ->limit(5)
            ->get();

        // Second query: DB COUNT — much cheaper than loading all rows
        // just to call ->count() on a PHP collection.
        $unreadCount = $admin->unreadNotifications()->count();

        $view->with([
            'adminUser' => $admin,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'pendingTestimonialsCount' => Testimonial::pending()->count(),
        ]);
    }
}
