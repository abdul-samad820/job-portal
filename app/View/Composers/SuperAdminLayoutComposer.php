<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SuperAdminLayoutComposer
{
    /**
     * Replaces the hardcoded "3 Notifications" placeholder and dead
     * profile dropdown that were previously static markup in
     * layouts/superadmin.blade.php.
     */
    public function compose(View $view): void
    {
        $superadmin = Auth::guard('superadmin')->user();

        if (! $superadmin) {
            $view->with(['superAdminUser' => null, 'saNotifications' => collect(), 'saUnreadCount' => 0]);

            return;
        }

        $notifications = $superadmin->unreadNotifications()->latest()->limit(5)->get();
        $unreadCount = $superadmin->unreadNotifications()->count();

        $view->with([
            'superAdminUser' => $superadmin,
            'saNotifications' => $notifications,
            'saUnreadCount' => $unreadCount,
        ]);
    }
}
