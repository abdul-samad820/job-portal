<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Notifications\SuperAdminTwoFactorCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SuperAdminLoginFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_login_2fa_dashboard_flow()
    {
        Notification::fake();

        $admin = Admin::factory()->create([
            'email' => 'super@test.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Step 1: submit login form
        $response = $this->post('/superadmin/login', [
            'email' => 'super@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('superadmin.2fa.form'));
        $this->assertTrue(session()->has('2fa_superadmin_id'));

        Notification::assertSentTo($admin, SuperAdminTwoFactorCodeNotification::class);

        // capture the code Laravel generated (via reflection on the notification)
        $sentCode = null;
        Notification::assertSentTo($admin, SuperAdminTwoFactorCodeNotification::class, function ($notification) use (&$sentCode) {
            $ref = new \ReflectionProperty($notification, 'code');
            $ref->setAccessible(true);
            $sentCode = $ref->getValue($notification);

            return true;
        });

        $this->assertNotNull($sentCode);

        // Step 2: verify 2fa code
        $response = $this->post('/superadmin/verify-2fa', [
            'code' => $sentCode,
        ]);

        $response->assertRedirect(route('superadmin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'superadmin'); // real proof: session is logged in on the superadmin guard

        // Step 3: follow the redirect using the SAME session (no manual actingAs)
        $dashboard = $this->get('/superadmin/dashboard');
        $dashboard->assertStatus(200);
    }

    public function test_wrong_password_rejected()
    {
        Admin::factory()->create([
            'email' => 'super2@test.com',
            'password' => Hash::make('correct-password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $response = $this->post('/superadmin/login', [
            'email' => 'super2@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(session()->has('2fa_superadmin_id'));
    }

    public function test_regular_admin_role_cannot_login_via_superadmin_form()
    {
        Admin::factory()->create([
            'email' => 'regular@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->post('/superadmin/login', [
            'email' => 'regular@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(session()->has('2fa_superadmin_id'));
    }

    public function test_suspended_superadmin_cannot_login()
    {
        Admin::factory()->create([
            'email' => 'suspended@test.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => false,
        ]);

        $response = $this->post('/superadmin/login', [
            'email' => 'suspended@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_wrong_2fa_code_rejected()
    {
        Notification::fake();

        $admin = Admin::factory()->create([
            'email' => 'super3@test.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->post('/superadmin/login', [
            'email' => 'super3@test.com',
            'password' => 'password123',
        ]);

        $response = $this->post('/superadmin/verify-2fa', [
            'code' => '000000',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_dashboard_redirects_guest_to_login()
    {
        $response = $this->get('/superadmin/dashboard');
        $response->assertRedirect(route('superadmin.login.view'));
    }
}
