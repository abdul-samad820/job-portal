<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

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
        // Suspended admin factory state use karo
        Admin::factory()->suspended()->create([
            'email' => 'suspended@company.com',
            'password' => bcrypt('Admin@123'),
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => 'suspended@company.com',
            'password' => 'Admin@123',
        ]);

        // Login nahi hua
        $this->assertGuest('admin');

        // Suspend error aaya
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
