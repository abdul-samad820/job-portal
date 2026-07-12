<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Faq;
use App\Models\Job;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ProfileCompletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression tests for bugs found during the Phase 1-10 code audit that
 * previously had no test coverage (Phase12 testing-gap review).
 */
class AuditRegressionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function saved_jobs_api_does_not_expose_admin_password(): void
    {
        $user = User::factory()->create();
        $admin = Admin::factory()->create();
        $job = Job::factory()->create(['admin_id' => $admin->id]);

        $user->savedJobs()->attach($job->id);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/saved-jobs');

        $response->assertStatus(200);
        $response->assertJsonMissingPath('data.0.job.password');
        $this->assertStringNotContainsString(
            $admin->password,
            $response->getContent()
        );
    }

    #[Test]
    public function profile_completion_is_consistent_web_vs_api(): void
    {
        $user = User::factory()->create(['phone' => '9999999999', 'address' => 'Test City']);

        UserProfile::create([
            'user_id' => $user->id,
            'profile_image' => 'photo.jpg',
            'professional_summary' => 'A summary',
            'core_skills' => 'PHP, Laravel',
            'education' => [['degree' => 'BCA']],
            'experience' => [['role' => 'Developer']],
            'projects' => [['name' => 'Project 1']],
        ]);

        $webCompletion = ProfileCompletionService::calculate($user->fresh());

        $apiResponse = $this->actingAs($user, 'sanctum')->getJson('/api/v1/profile');
        $apiCompletion = (int) str_replace('%', '', $apiResponse->json('data.profile_completion'));

        $this->assertSame($webCompletion, $apiCompletion);
        $this->assertSame(100, $webCompletion);
    }

    #[Test]
    public function unauthenticated_user_cannot_create_faq(): void
    {
        $response = $this->post(route('faqs_create'), [
            'question' => 'Is this legit?',
            'answer' => 'Yes',
        ]);

        $response->assertRedirect(); // redirected to login, not 200/created
        $this->assertDatabaseMissing('faqs', ['question' => 'Is this legit?']);
    }

    #[Test]
    public function unauthenticated_user_cannot_delete_faq(): void
    {
        $admin = Admin::factory()->create();
        $faq = Faq::create([
            'admin_id' => $admin->id,
            'question' => 'Q',
            'answer' => 'A',
            'status' => 1,
        ]);

        $this->delete(route('faqs_delete', $faq->id));

        $this->assertDatabaseHas('faqs', ['id' => $faq->id]);
    }

    #[Test]
    public function user_cannot_change_password_without_current_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('OldPassword@123'),
            'phone' => '9999999999',
        ]);

        $response = $this->actingAs($user, 'user')
            ->post(route('user.account_setting_update'), [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '9999999999',
                'password' => 'NewPassword@123',
                'password_confirmation' => 'NewPassword@123',
                // current_password intentionally omitted
            ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('OldPassword@123', $user->fresh()->password));
    }

    #[Test]
    public function admin_password_is_never_serialized(): void
    {
        $admin = Admin::factory()->create();

        $this->assertArrayNotHasKey('password', $admin->toArray());
        $this->assertArrayNotHasKey('remember_token', $admin->toArray());
    }

    #[Test]
    public function suspended_user_cannot_login_via_api(): void
    {
        $user = User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('Password@123'),
            'is_active' => false,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'Password@123',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function suspended_user_token_is_revoked_on_next_api_request(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $user->forceFill(['is_active' => false])->save();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/v1/auth/me');

        $response->assertStatus(403);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function admin_suspend_revokes_users_api_tokens(): void
    {
        $superadmin = Admin::factory()->create(['role' => 'super_admin']);
        $user = User::factory()->create();
        $user->createToken('test');

        $this->actingAs($superadmin, 'superadmin')
            ->patch(route('superadmin.users.suspend', $user->id));

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
