<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Services\ProfileCompletionService;
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
        $completion = ProfileCompletionService::calculate($user);

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'profile' => $profile ? [
                'professional_summary' => $profile->professional_summary,
                'core_skills' => $profile->core_skills,
                'education' => $profile->education ?? [],
                'experience' => $profile->experience ?? [],
                'projects' => $profile->projects ?? [],
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
        $existing = UserProfile::where('user_id', $userId)->first();

        // Only overwrite a field if it was actually present in this
        // request. Without this, a PATCH request that sends only one
        // field (e.g. { "professional_summary": "..." }) would silently
        // wipe every other field on the profile to null/empty, because
        // updateOrCreate() previously always wrote all 5 keys regardless
        // of whether the client sent them.
        $profile = UserProfile::updateOrCreate(
            ['user_id' => $userId],
            [
                'professional_summary' => $request->has('professional_summary')
                    ? $request->professional_summary
                    : $existing?->professional_summary,
                'core_skills' => $request->has('core_skills')
                    ? $request->core_skills
                    : $existing?->core_skills,
                'education' => $request->has('education')
                    ? $request->education
                    : ($existing?->education ?? []),
                'experience' => $request->has('experience')
                    ? $request->experience
                    : ($existing?->experience ?? []),
                'projects' => $request->has('projects')
                    ? $request->projects
                    : ($existing?->projects ?? []),
            ]
        );

        return $this->success([
            'professional_summary' => $profile->professional_summary,
            'core_skills' => $profile->core_skills,
        ], 'Profile updated successfully!');
    }
}
