<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Notifications\SuperAdminTwoFactorCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Fills the last gaps in SuperAdmin route coverage that weren't hit by
 * the other SuperAdmin*Test files: logout, 2FA code resend, and the
 * "edit admin" form page (its PUT counterpart is already covered in
 * SuperAdminAdminManagementTest).
 */
class SuperAdminAuthGapsTest extends TestCase
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
    public function logout_ends_the_superadmin_session_and_redirects_to_login(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.logout'));

        $response->assertRedirect(route('superadmin.login.view'));

        // Guard should no longer be authenticated on the next request.
        $this->assertGuest('superadmin');
    }

    #[Test]
    public function logged_out_superadmin_cannot_reach_the_dashboard_again(): void
    {
        $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.logout'));

        $response = $this->get(route('superadmin.dashboard'));

        $response->assertRedirect(route('superadmin.login.view'));
    }

    #[Test]
    public function two_factor_resend_sends_a_new_code_when_session_is_pending(): void
    {
        Notification::fake();

        // Simulate the mid-login state: password already verified,
        // waiting on the 2FA code, before Auth::guard('superadmin')->login().
        $this->withSession(['2fa_superadmin_id' => $this->superAdmin->id])
            ->post(route('superadmin.2fa.resend'))
            ->assertRedirect()
            ->assertSessionHas('success');

        Notification::assertSentTo($this->superAdmin, SuperAdminTwoFactorCodeNotification::class);
        $this->assertNotNull($this->superAdmin->fresh()->two_factor_code);
    }

    #[Test]
    public function two_factor_resend_without_a_pending_login_redirects_to_login_form(): void
    {
        $response = $this->post(route('superadmin.2fa.resend'));

        $response->assertRedirect(route('superadmin.login.view'));
    }

    #[Test]
    public function two_factor_resend_issues_a_different_code_than_before(): void
    {
        $this->superAdmin->forceFill([
            'two_factor_code' => bcrypt('111111'),
            'two_factor_expires_at' => now()->addMinutes(10),
        ])->save();
        $originalHash = $this->superAdmin->two_factor_code;

        $this->withSession(['2fa_superadmin_id' => $this->superAdmin->id])
            ->post(route('superadmin.2fa.resend'));

        $this->assertNotEquals($originalHash, $this->superAdmin->fresh()->two_factor_code);
    }

    #[Test]
    public function edit_admin_form_renders_for_an_existing_admin(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin', 'company_name' => 'Editable Co']);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.admin.edit', $admin->id));

        $response->assertStatus(200);
        $response->assertViewHas('admin', function ($viewAdmin) use ($admin) {
            return $viewAdmin->id === $admin->id;
        });
    }

    #[Test]
    public function edit_admin_form_404s_for_a_nonexistent_admin(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.admin.edit', 999999));

        $response->assertStatus(404);
    }

    #[Test]
    public function edit_admin_form_404s_when_id_belongs_to_a_superadmin_not_a_company_admin(): void
    {
        $anotherSuperAdmin = Admin::factory()->create(['role' => 'super_admin']);

        // editForm() scopes to role=admin, so a super_admin's own id
        // must not be editable through the company-admin edit screen.
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.admin.edit', $anotherSuperAdmin->id));

        $response->assertStatus(404);
    }

    #[Test]
    public function guest_cannot_reach_the_edit_admin_form(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);

        $response = $this->get(route('superadmin.admin.edit', $admin->id));

        $response->assertRedirect(route('superadmin.login.view'));
    }
}
