<?php

namespace App\Http\Controllers;

use App\Models\CompanyFollow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyFollowController extends Controller
{
    /**
     * Follow/unfollow toggle for a company (Admin account). Redirects
     * back to wherever the button was clicked from (job page, company
     * card, etc).
     */
    public function toggle(Request $request, $adminId)
    {
        $userId = Auth::guard('user')->id();
        $user = Auth::guard('user')->user();

        $existing = CompanyFollow::where('user_id', $userId)->where('admin_id', $adminId)->first();

        if ($existing) {
            $existing->delete();
            $message = 'Unfollowed company.';
        } else {
            $limit = \App\Services\BadgeLimitService::limitFor($user, 'company_follow');
            $currentCount = CompanyFollow::where('user_id', $userId)->count();

            if ($limit !== null && $currentCount >= $limit) {
                return back()->with('error',
                    "You can follow up to {$limit} companies. Unfollow one, or refer more friends to raise this limit.");
            }

            CompanyFollow::create(['user_id' => $userId, 'admin_id' => $adminId]);
            $message = 'You are now following this company. You\'ll be notified about their new job posts.';
        }

        return back()->with('success', $message);
    }

    /**
     * List of companies the logged-in user currently follows.
     */
    public function index()
    {
        $userId = Auth::guard('user')->id();
        $follows = CompanyFollow::with('company')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return view('User.following', compact('follows'));
    }
}
