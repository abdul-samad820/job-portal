<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserLayoutComposer
{
    public function compose(View $view): void
    {
        if (! auth('user')->check()) {
            $view->with([
                'authUser' => null,
                'userImg' => asset('admins/dist/img/default.png'),
                'notifications' => collect(),
                'unreadCount' => 0,
                'navBadgeTier' => null,
            ]);

            return;
        }

        $user = auth('user')->user();
        $profile = $user->profile ?? null;

        $userImg = ($profile && $profile->profile_image)
            ? Storage::url('user_profile/'.$profile->profile_image)
            : asset('admins/dist/img/default.png');

        $notifications = $user->unreadNotifications()
            ->latest()
            ->limit(5)
            ->get();

        $unreadCount = $user->unreadNotifications()->count();

        $navBadgeTier = \App\Services\BadgeLimitService::tierFor($user);

        $view->with([
            'authUser' => $user,
            'userImg' => $userImg,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'navBadgeTier' => $navBadgeTier,
        ]);
    }
}
