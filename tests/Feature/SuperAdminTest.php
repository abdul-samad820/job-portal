<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuperAdminTest extends TestCase
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
    public function superadmin_can_suspend_an_admin(): void
    {
        $admin = Admin::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.admin.suspend', $admin->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'is_active' => 0,
        ]);
    }

    #[Test]
    public function superadmin_can_reactivate_suspended_admin(): void
    {
        $admin = Admin::factory()->suspended()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.admin.unsuspend', $admin->id));

        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'is_active' => 1,
        ]);
    }

    #[Test]
    public function superadmin_can_delete_an_admin(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->delete(route('superadmin.admin.delete', $admin->id));

        $this->assertDatabaseMissing('admins', ['id' => $admin->id]);
    }

    #[Test]
    public function regular_admin_cannot_access_superadmin_routes(): void
    {
        $regularAdmin = Admin::factory()->create(['role' => 'admin']);
        $response = $this->get(route('superadmin.dashboard'));
        $response->assertRedirect();
    }
}
