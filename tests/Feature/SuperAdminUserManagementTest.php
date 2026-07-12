<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuperAdminUserManagementTest extends TestCase
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
    public function user_directory_lists_users_with_search_and_status_filters(): void
    {
        User::factory()->create(['name' => 'Alice Job Seeker', 'is_active' => true]);
        User::factory()->create(['name' => 'Bob Suspended', 'is_active' => false]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.users', ['search' => 'Alice']));

        $users = $response->viewData('users');
        $this->assertCount(1, $users);
        $this->assertEquals('Alice Job Seeker', $users->first()->name);

        $suspendedResponse = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.users', ['status' => 'suspended']));

        $suspendedUsers = $suspendedResponse->viewData('users');
        $this->assertCount(1, $suspendedUsers);
        $this->assertEquals('Bob Suspended', $suspendedUsers->first()->name);
    }

    #[Test]
    public function superadmin_can_suspend_a_user_and_it_revokes_their_api_tokens(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->createToken('mobile-app');

        $this->assertEquals(1, $user->tokens()->count());

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.users.suspend', $user->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => 0]);
        $this->assertEquals(0, $user->tokens()->count());
    }

    #[Test]
    public function superadmin_can_unsuspend_a_user(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.users.unsuspend', $user->id));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => 1]);
    }

    #[Test]
    public function suspending_a_user_is_logged_in_activity_log(): void
    {
        $user = User::factory()->create(['name' => 'Track Me']);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.users.suspend', $user->id));

        $this->assertDatabaseHas('admin_activity_logs', [
            'action' => 'user_suspended',
        ]);
    }

    #[Test]
    public function users_csv_export_streams_a_csv_response(): void
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.users.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    #[Test]
    public function suspending_a_nonexistent_user_returns_404(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.users.suspend', 999999));

        $response->assertStatus(404);
    }

    #[Test]
    public function regular_admin_cannot_manage_platform_users(): void
    {
        $user = User::factory()->create();

        $response = $this->patch(route('superadmin.users.suspend', $user->id));

        $response->assertRedirect(route('superadmin.login.view'));
    }
}
