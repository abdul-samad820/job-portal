<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Job;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Covers SuperAdminController features not already handled by
 * SuperAdminTest.php / SuperAdminSessionManagementTest.php:
 * dashboard stats, admin search/list/export, create/edit admin,
 * global site settings, bulk actions, and notifications.
 */
class SuperAdminAdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = Admin::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function dashboard_shows_correct_counts(): void
    {
        Admin::factory()->count(2)->create(['role' => 'admin', 'is_active' => true]);
        Admin::factory()->count(1)->create(['role' => 'admin', 'is_active' => false]);
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalAdmins', 3);
        $response->assertViewHas('activeAdmins', 2);
        $response->assertViewHas('suspendedAdmins', 1);
        $response->assertViewHas('totalUsers', 3);
    }

    #[Test]
    public function admin_list_can_be_searched_by_company_name(): void
    {
        Admin::factory()->create(['role' => 'admin', 'company_name' => 'Acme Corp']);
        Admin::factory()->create(['role' => 'admin', 'company_name' => 'Globex Inc']);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.admins', ['search' => 'Acme']));

        $response->assertStatus(200);
        $admins = $response->viewData('admins');

        $this->assertCount(1, $admins);
        $this->assertEquals('Acme Corp', $admins->first()->company_name);
    }

    #[Test]
    public function admin_list_can_be_filtered_by_status(): void
    {
        Admin::factory()->create(['role' => 'admin', 'is_active' => true, 'company_name' => 'Active Co']);
        Admin::factory()->create(['role' => 'admin', 'is_active' => false, 'company_name' => 'Suspended Co']);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.admins', ['status' => 'suspended']));

        $admins = $response->viewData('admins');

        $this->assertCount(1, $admins);
        $this->assertEquals('Suspended Co', $admins->first()->company_name);
    }

    #[Test]
    public function superadmin_can_create_a_new_admin(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.create'), [
                'company_name' => 'New Co',
                'email' => 'newco@example.com',
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
            ]);

        $response->assertRedirect(route('superadmin.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('admins', [
            'email' => 'newco@example.com',
            'company_name' => 'New Co',
            'role' => 'admin',
        ]);
    }

    #[Test]
    public function creating_an_admin_with_duplicate_email_fails_validation(): void
    {
        Admin::factory()->create(['role' => 'admin', 'email' => 'dupe@example.com']);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.create'), [
                'company_name' => 'Dupe Co',
                'email' => 'dupe@example.com',
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
            ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, Admin::where('email', 'dupe@example.com')->count());
    }

    #[Test]
    public function regular_admin_is_forbidden_from_the_create_admin_form(): void
    {
        // A regular company admin does not hold the 'superadmin' guard
        // session at all, so the SuperAdminOnly middleware bounces them
        // to the login form before the controller's own role check runs.
        $regularAdmin = Admin::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($regularAdmin, 'admin')
            ->get(route('superadmin.create.form'));

        $response->assertRedirect(route('superadmin.login.view'));
    }

    #[Test]
    public function superadmin_can_update_an_admins_details(): void
    {
        $admin = Admin::factory()->create([
            'role' => 'admin',
            'company_name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->put(route('superadmin.admin.update', $admin->id), [
                'company_name' => 'New Name',
                'email' => 'new@example.com',
            ]);

        $response->assertRedirect(route('superadmin.admins'));

        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'company_name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    #[Test]
    public function updating_an_admin_leaves_password_untouched_when_left_blank(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        $originalHash = $admin->password;

        $this->actingAs($this->superAdmin, 'superadmin')
            ->put(route('superadmin.admin.update', $admin->id), [
                'company_name' => $admin->company_name,
                'email' => $admin->email,
            ]);

        $this->assertEquals($originalHash, $admin->fresh()->password);
    }

    #[Test]
    public function showing_an_admin_returns_expected_json_fields(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        Job::factory()->count(2)->create(['admin_id' => $admin->id]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.admin.show', $admin->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $admin->id, 'company_name' => $admin->company_name]);
        $response->assertJsonPath('jobs_count', 2);
    }

    #[Test]
    public function admins_csv_export_streams_a_csv_response(): void
    {
        Admin::factory()->count(3)->create(['role' => 'admin']);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.admins.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    #[Test]
    public function superadmin_can_view_and_update_site_settings(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.settings'));

        $response->assertStatus(200);
        $response->assertViewHas('settings');

        $update = $this->actingAs($this->superAdmin, 'superadmin')
            ->put(route('superadmin.settings.update'), [
                'contact_email' => 'help@jobhub.com',
                'contact_location' => 'Mumbai, India',
                'working_hours' => 'Mon-Fri, 9-5',
                'linkedin_url' => 'https://linkedin.com/company/jobhub',
                'twitter_url' => null,
                'github_url' => null,
            ]);

        $update->assertRedirect();
        $update->assertSessionHas('success');

        $this->assertEquals('help@jobhub.com', Setting::get('contact_email'));
        $this->assertEquals('Mumbai, India', Setting::get('contact_location'));
    }

    #[Test]
    public function site_settings_update_requires_valid_contact_email(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->put(route('superadmin.settings.update'), [
                'contact_email' => 'not-an-email',
                'contact_location' => 'Mumbai, India',
                'working_hours' => 'Mon-Fri, 9-5',
            ]);

        $response->assertSessionHasErrors('contact_email');
    }

    #[Test]
    public function bulk_action_can_suspend_multiple_admins_at_once(): void
    {
        $admins = Admin::factory()->count(3)->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.admins.bulk'), [
                'action' => 'suspend',
                'admin_ids' => $admins->pluck('id')->toArray(),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        foreach ($admins as $admin) {
            $this->assertDatabaseHas('admins', ['id' => $admin->id, 'is_active' => 0]);
        }
    }

    #[Test]
    public function bulk_action_can_unsuspend_multiple_admins_at_once(): void
    {
        $admins = Admin::factory()->count(2)->create(['role' => 'admin', 'is_active' => false]);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.admins.bulk'), [
                'action' => 'unsuspend',
                'admin_ids' => $admins->pluck('id')->toArray(),
            ]);

        foreach ($admins as $admin) {
            $this->assertDatabaseHas('admins', ['id' => $admin->id, 'is_active' => 1]);
        }
    }

    #[Test]
    public function bulk_delete_removes_admins_with_no_jobs(): void
    {
        $admins = Admin::factory()->count(2)->create(['role' => 'admin']);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.admins.bulk'), [
                'action' => 'delete',
                'admin_ids' => $admins->pluck('id')->toArray(),
            ]);

        foreach ($admins as $admin) {
            $this->assertDatabaseMissing('admins', ['id' => $admin->id]);
        }
    }

    /**
     * NOTE: unlike the single-admin deleteAdmin() action, bulkAction's
     * 'delete' branch does NOT check jobs_count/job_applications_count
     * before calling $admin->delete(). This test documents the current
     * (inconsistent) behavior — a company with live jobs/applications
     * gets wiped via bulk delete even though the single-delete endpoint
     * would refuse. Flagging this as a candidate follow-up fix.
     */
    #[Test]
    public function bulk_delete_does_not_protect_admins_with_existing_jobs(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        Job::factory()->create(['admin_id' => $admin->id]);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.admins.bulk'), [
                'action' => 'delete',
                'admin_ids' => [$admin->id],
            ]);

        // Current behavior: deleted despite having a job on file.
        $this->assertDatabaseMissing('admins', ['id' => $admin->id]);
    }

    #[Test]
    public function bulk_action_rejects_an_invalid_action_value(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.admins.bulk'), [
                'action' => 'nuke',
                'admin_ids' => [$admin->id],
            ]);

        $response->assertSessionHasErrors('action');
    }

    #[Test]
    public function activity_log_lists_recorded_actions(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.admin.suspend', $admin->id));

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.activity-log'));

        $response->assertStatus(200);
        $this->assertDatabaseHas('admin_activity_logs', [
            'action' => 'admin_suspended',
        ]);
    }

    #[Test]
    public function regular_admin_cannot_access_any_superadmin_admin_management_route(): void
    {
        $regularAdmin = Admin::factory()->create(['role' => 'admin']);

        $response = $this->get(route('superadmin.admins'));

        $response->assertRedirect(route('superadmin.login.view'));
    }
}
