<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobInvite;
use App\Models\JobRole;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();
    }

    // ===================== PROFILE =====================

    #[Test]
    public function admin_can_view_own_profile(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.profile'));

        $response->assertOk();
        $response->assertViewHas('profile');
    }

    #[Test]
    public function admin_can_update_profile(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.profile.update'), [
                'company_name' => 'Updated Company Pvt Ltd',
                'email' => $this->admin->email,
                'location' => 'Noida',
                'expertise' => 'Laravel, PHP',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('admins', [
            'id' => $this->admin->id,
            'company_name' => 'Updated Company Pvt Ltd',
            'location' => 'Noida',
        ]);
    }

    #[Test]
    public function admin_cannot_update_profile_with_email_used_by_another_admin(): void
    {
        $otherAdmin = Admin::factory()->create(['email' => 'taken@company.com']);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.profile.update'), [
                'company_name' => 'Company',
                'email' => 'taken@company.com',
            ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('admins', [
            'id' => $this->admin->id,
            'email' => 'taken@company.com',
        ]);
    }

    #[Test]
    public function admin_cannot_update_profile_without_required_company_name(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.profile.update'), [
                'company_name' => '',
                'email' => $this->admin->email,
            ]);

        $response->assertSessionHasErrors('company_name');
    }

    // ===================== FORCE PASSWORD CHANGE =====================

    #[Test]
    public function admin_can_set_new_password_on_force_change(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.force_password.update'), [
                'password' => 'NewPass123',
                'password_confirmation' => 'NewPass123',
            ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->admin->refresh();
        $this->assertFalse($this->admin->must_change_password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NewPass123', $this->admin->password));
    }

    #[Test]
    public function force_password_update_requires_confirmation_match(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.force_password.update'), [
                'password' => 'NewPass123',
                'password_confirmation' => 'Mismatch123',
            ]);

        $response->assertSessionHasErrors('password');
    }

    // ===================== ACTIVITY LOG =====================

    #[Test]
    public function admin_activity_log_only_shows_own_entries(): void
    {
        $otherAdmin = Admin::factory()->create();

        \App\Models\ActivityLog::create([
            'admin_id' => $this->admin->id,
            'action' => 'job_created',
            'description' => 'Created a job for me',
        ]);
        \App\Models\ActivityLog::create([
            'admin_id' => $otherAdmin->id,
            'action' => 'job_created',
            'description' => 'Created a job for other admin',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.activity_log'));

        $response->assertOk();
        $logs = $response->viewData('logs');
        $this->assertCount(1, $logs);
        $this->assertEquals('Created a job for me', $logs->first()->description);
    }

    // ===================== SEARCH =====================

    #[Test]
    public function admin_search_only_returns_own_jobs_and_applicants(): void
    {
        $otherAdmin = Admin::factory()->create();

        $myJob = Job::factory()->create(['admin_id' => $this->admin->id, 'title' => 'Laravel Developer']);
        Job::factory()->create(['admin_id' => $otherAdmin->id, 'title' => 'Laravel Engineer']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.search', ['q' => 'Laravel']));

        $response->assertOk();
        $jobs = $response->viewData('jobs');
        $this->assertCount(1, $jobs);
        $this->assertEquals($myJob->id, $jobs->first()->id);
    }

    #[Test]
    public function admin_search_with_empty_query_returns_no_results(): void
    {
        Job::factory()->create(['admin_id' => $this->admin->id, 'title' => 'Laravel Developer']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.search', ['q' => '']));

        $response->assertOk();
        $this->assertCount(0, $response->viewData('jobs'));
    }

    // ===================== SELECTED LIST =====================

    #[Test]
    public function admin_selected_list_only_shows_shortlisted_and_hired_applicants(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);

        $shortlisted = JobApplication::factory()->create(['job_id' => $job->id, 'status' => 'shortlisted']);
        $hired = JobApplication::factory()->create(['job_id' => $job->id, 'status' => 'hired']);
        JobApplication::factory()->create(['job_id' => $job->id, 'status' => 'pending']);
        JobApplication::factory()->create(['job_id' => $job->id, 'status' => 'rejected']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.selectedList'));

        $response->assertOk();
        $selected = $response->viewData('selectedApplicants');
        $this->assertCount(2, $selected);
        $this->assertTrue($selected->pluck('id')->contains($shortlisted->id));
        $this->assertTrue($selected->pluck('id')->contains($hired->id));
    }

    // ===================== JOB CATEGORY =====================

    #[Test]
    public function admin_can_create_a_job_category(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.job_category_create'), [
                'name' => 'Information Technology',
                'description' => 'IT related roles',
            ]);

        $response->assertRedirect(route('admin.job_category'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('job_categories', [
            'name' => 'Information Technology',
            'admin_id' => $this->admin->id,
        ]);
    }

    #[Test]
    public function admin_cannot_create_job_category_without_name(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.job_category_create'), [
                'description' => 'No name here',
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function admin_can_update_own_job_category(): void
    {
        $category = JobCategory::factory()->create(['admin_id' => $this->admin->id, 'name' => 'Old Name']);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.job_category_update', $category->id), [
                'name' => 'New Name',
            ]);

        $response->assertRedirect(route('admin.job_category'));
        $this->assertDatabaseHas('job_categories', ['id' => $category->id, 'name' => 'New Name']);
    }

    #[Test]
    public function admin_cannot_update_another_admins_job_category(): void
    {
        $otherAdmin = Admin::factory()->create();
        $category = JobCategory::factory()->create(['admin_id' => $otherAdmin->id, 'name' => 'Old Name']);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.job_category_update', $category->id), [
                'name' => 'Hacked Name',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('job_categories', ['id' => $category->id, 'name' => 'Old Name']);
    }

    #[Test]
    public function admin_can_delete_own_job_category_with_no_jobs(): void
    {
        $category = JobCategory::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.job_category_delete', $category->id));

        $response->assertRedirect(route('admin.job_category'));
        $this->assertDatabaseMissing('job_categories', ['id' => $category->id]);
    }

    #[Test]
    public function admin_cannot_delete_job_category_that_still_has_jobs(): void
    {
        $category = JobCategory::factory()->create(['admin_id' => $this->admin->id]);
        Job::factory()->create(['admin_id' => $this->admin->id, 'category_id' => $category->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.job_category_delete', $category->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('job_categories', ['id' => $category->id]);
    }

    #[Test]
    public function admin_cannot_delete_another_admins_job_category(): void
    {
        $otherAdmin = Admin::factory()->create();
        $category = JobCategory::factory()->create(['admin_id' => $otherAdmin->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.job_category_delete', $category->id));

        $response->assertForbidden();
        $this->assertDatabaseHas('job_categories', ['id' => $category->id]);
    }

    // ===================== JOB ROLE =====================

    #[Test]
    public function admin_can_create_a_job_role_under_own_category(): void
    {
        $category = JobCategory::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.job_role_create'), [
                'name' => 'Backend Developer',
                'description' => 'Server-side role',
                'category_id' => $category->id,
            ]);

        $response->assertRedirect(route('admin.job_role'));
        $this->assertDatabaseHas('job_roles', [
            'name' => 'Backend Developer',
            'admin_id' => $this->admin->id,
            'category_id' => $category->id,
        ]);
    }

    #[Test]
    public function admin_cannot_create_job_role_using_another_admins_category(): void
    {
        $otherAdmin = Admin::factory()->create();
        $category = JobCategory::factory()->create(['admin_id' => $otherAdmin->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.job_role_create'), [
                'name' => 'Backend Developer',
                'category_id' => $category->id,
            ]);

        $response->assertSessionHasErrors('category_id');
        $this->assertDatabaseMissing('job_roles', ['name' => 'Backend Developer']);
    }

    #[Test]
    public function admin_can_update_own_job_role(): void
    {
        $category = JobCategory::factory()->create(['admin_id' => $this->admin->id]);
        $role = JobRole::factory()->create(['admin_id' => $this->admin->id, 'category_id' => $category->id, 'name' => 'Old Role']);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.job_role_update', $role->id), [
                'name' => 'New Role',
                'category_id' => $category->id,
            ]);

        $response->assertRedirect(route('admin.job_role'));
        $this->assertDatabaseHas('job_roles', ['id' => $role->id, 'name' => 'New Role']);
    }

    #[Test]
    public function admin_cannot_update_another_admins_job_role(): void
    {
        $otherAdmin = Admin::factory()->create();
        $category = JobCategory::factory()->create(['admin_id' => $otherAdmin->id]);
        $role = JobRole::factory()->create(['admin_id' => $otherAdmin->id, 'category_id' => $category->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.job_role_update', $role->id), [
                'name' => 'Hacked Role',
                'category_id' => $category->id,
            ]);

        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_delete_own_job_role(): void
    {
        $role = JobRole::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.job_role_delete', $role->id));

        $response->assertRedirect(route('admin.job_role'));
        $this->assertDatabaseMissing('job_roles', ['id' => $role->id]);
    }

    #[Test]
    public function admin_cannot_delete_another_admins_job_role(): void
    {
        $otherAdmin = Admin::factory()->create();
        $role = JobRole::factory()->create(['admin_id' => $otherAdmin->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.job_role_delete', $role->id));

        $response->assertForbidden();
        $this->assertDatabaseHas('job_roles', ['id' => $role->id]);
    }

    // ===================== CANDIDATES =====================

    #[Test]
    public function admin_can_browse_only_open_to_work_candidates(): void
    {
        $openUser = User::factory()->create();
        UserProfile::create(['user_id' => $openUser->id, 'open_to_work' => true]);

        $closedUser = User::factory()->create();
        UserProfile::create(['user_id' => $closedUser->id, 'open_to_work' => false]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.candidates.index'));

        $response->assertOk();
        $candidates = $response->viewData('candidates');
        $this->assertCount(1, $candidates);
        $this->assertEquals($openUser->id, $candidates->first()->id);
    }

    #[Test]
    public function admin_can_invite_candidate_to_own_job(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);
        $candidate = User::factory()->create();
        UserProfile::create(['user_id' => $candidate->id, 'open_to_work' => true]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.candidates.invite', $candidate->id), [
                'job_id' => $job->id,
                'message' => 'We would love to have you apply!',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('job_invites', [
            'job_id' => $job->id,
            'user_id' => $candidate->id,
            'admin_id' => $this->admin->id,
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function admin_cannot_invite_candidate_using_another_admins_job(): void
    {
        $otherAdmin = Admin::factory()->create();
        $job = Job::factory()->create(['admin_id' => $otherAdmin->id]);
        $candidate = User::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.candidates.invite', $candidate->id), [
                'job_id' => $job->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('job_invites', [
            'job_id' => $job->id,
            'user_id' => $candidate->id,
        ]);
    }

    #[Test]
    public function admin_cannot_invite_same_candidate_twice_for_same_job(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);
        $candidate = User::factory()->create();

        JobInvite::create([
            'job_id' => $job->id,
            'user_id' => $candidate->id,
            'admin_id' => $this->admin->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.candidates.invite', $candidate->id), [
                'job_id' => $job->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('job_invites', 1);
    }

    // ===================== NOTIFICATIONS =====================

    #[Test]
    public function admin_can_mark_notifications_as_read(): void
    {
        $this->admin->notify(new \App\Notifications\LoginSecurityNotification('127.0.0.1', 'PHPUnit'));
        $this->assertEquals(1, $this->admin->fresh()->unreadNotifications()->count());

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.notifications.read'));

        $response->assertRedirect();
        $this->assertEquals(0, $this->admin->fresh()->unreadNotifications()->count());
    }

    // ===================== ACCESS CONTROL =====================

    #[Test]
    public function guest_cannot_access_admin_job_category_page(): void
    {
        $response = $this->get(route('admin.job_category'));

        $response->assertRedirect(route('admin.login.view'));
    }

    #[Test]
    public function guest_cannot_access_admin_candidates_page(): void
    {
        $response = $this->get(route('admin.candidates.index'));

        $response->assertRedirect(route('admin.login.view'));
    }
}
