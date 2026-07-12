<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_login_with_valid_credentials(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@company.com',
            'password' => bcrypt('Admin@123'),
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => 'admin@company.com',
            'password' => 'Admin@123',
        ]);

        $this->assertAuthenticatedAs($admin, 'admin');
        $response->assertRedirect(route('admin.dashboard'));
    }

    #[Test]
    public function suspended_admin_cannot_login(): void
    {
        // Use the suspended admin factory state
        Admin::factory()->suspended()->create([
            'email' => 'suspended@company.com',
            'password' => bcrypt('Admin@123'),
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => 'suspended@company.com',
            'password' => 'Admin@123',
        ]);

        // Login should not succeed
        $this->assertGuest('admin');

        // A suspension error should be returned
        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function admin_cannot_access_dashboard_when_guest(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login.view'));
    }

    #[Test]
    public function admin_can_logout(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.logout'));

        $this->assertGuest('admin');
    }
}
