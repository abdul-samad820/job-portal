<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProfileApiController extends Controller
{
    use ApiResponse;

    // ─────────────────────────────────────
    // Profile Show
    // ─────────────────────────────────────
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        // Profile completion calculate
        $completion = 0;
        if ($profile) {
            if (! empty($profile->profile_image)) {
                $completion += 15;
            }
            if (! empty($profile->professional_summary)) {
                $completion += 20;
            }
            if (! empty($profile->core_skills)) {
                $completion += 20;
            }
            if (! empty($profile->education)) {
                $completion += 20;
            }
            if (! empty($profile->experience)) {
                $completion += 15;
            }
            if (! empty($profile->projects)) {
                $completion += 10;
            }
        }

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'profile' => $profile ? [
                'professional_summary' => $profile->professional_summary,
                'core_skills' => $profile->core_skills,
                'education' => json_decode($profile->education ?? '[]'),
                'experience' => json_decode($profile->experience ?? '[]'),
                'projects' => json_decode($profile->projects ?? '[]'),
                'profile_image' => $profile->profile_image
                    ? asset('storage/'.$profile->profile_image)
                    : null,
            ] : null,
            'profile_completion' => $completion.'%',
        ], 'Profile fetched successfully.');
    }

    // ─────────────────────────────────────
    // Profile Update
    // ─────────────────────────────────────
    public function update(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'professional_summary' => 'nullable|string|max:1000',
                'core_skills' => 'nullable|string|max:500',
                'education' => 'nullable|array',
                'experience' => 'nullable|array',
                'projects' => 'nullable|array',
            ]);
        } catch (ValidationException $e) {
            return $this->error('Validation failed.', 422, $e->errors());
        }

        $userId = $request->user()->id;
        $profile = UserProfile::updateOrCreate(
            ['user_id' => $userId],
            [
                'professional_summary' => $request->professional_summary,
                'core_skills' => $request->core_skills,
                'education' => json_encode($request->education ?? []),
                'experience' => json_encode($request->experience ?? []),
                'projects' => json_encode($request->projects ?? []),
            ]
        );

        return $this->success([
            'professional_summary' => $profile->professional_summary,
            'core_skills' => $profile->core_skills,
        ], 'Profile updated successfully!');
    }
}
