<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Covers the part of SuperAdminController that ties directly into
 * Admin's "My Devices" feature: suspending a company admin must kill
 * their active sessions immediately (killAdminSessions), not just flip
 * is_active and wait for their next request to be caught by middleware.
 */
class SuperAdminSessionManagementTest extends TestCase
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

    /**
     * Simulate a logged-in admin by inserting a row into the sessions
     * table exactly the way AdminController::login tags it — with an
     * `admin_session_owner` key in the serialized payload.
     */
    private function fakeAdminSession(string $id, int $adminId, string $userAgent = 'Mozilla/5.0 Test Browser'): void
    {
        $payload = serialize([
            'admin_session_owner' => $adminId,
            '_token' => 'test-token',
        ]);

        DB::table('sessions')->insert([
            'id' => $id,
            'user_id' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => $userAgent,
            'payload' => base64_encode($payload),
            'last_activity' => now()->timestamp,
        ]);
    }

    #[Test]
    public function suspending_an_admin_deletes_their_active_sessions(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->fakeAdminSession('device-1-session', $admin->id);
        $this->fakeAdminSession('device-2-session', $admin->id);

        $this->assertDatabaseHas('sessions', ['id' => 'device-1-session']);
        $this->assertDatabaseHas('sessions', ['id' => 'device-2-session']);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.admin.suspend', $admin->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('sessions', ['id' => 'device-1-session']);
        $this->assertDatabaseMissing('sessions', ['id' => 'device-2-session']);

        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'is_active' => 0,
        ]);
    }

    #[Test]
    public function suspending_an_admin_does_not_touch_another_admins_sessions(): void
    {
        $suspended = Admin::factory()->create(['role' => 'admin', 'is_active' => true]);
        $untouched = Admin::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->fakeAdminSession('suspended-device', $suspended->id);
        $this->fakeAdminSession('untouched-device', $untouched->id);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.admin.suspend', $suspended->id));

        $this->assertDatabaseMissing('sessions', ['id' => 'suspended-device']);
        $this->assertDatabaseHas('sessions', ['id' => 'untouched-device']);
    }

    #[Test]
    public function suspending_an_admin_ignores_unreadable_session_rows_without_failing(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->fakeAdminSession('good-device', $admin->id);

        // Garbage payload that isn't valid base64/serialized data — the
        // suspend action must skip it instead of throwing.
        DB::table('sessions')->insert([
            'id' => 'corrupt-device',
            'user_id' => null,
            'ip_address' => '10.0.0.1',
            'user_agent' => 'Broken Client',
            'payload' => 'not-valid-base64-or-serialized-data!!!',
            'last_activity' => now()->timestamp,
        ]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.admin.suspend', $admin->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('sessions', ['id' => 'good-device']);
        // Corrupt row isn't owned by this admin (unreadable), so it's left alone.
        $this->assertDatabaseHas('sessions', ['id' => 'corrupt-device']);
    }

    #[Test]
    public function unsuspending_an_admin_does_not_kill_sessions(): void
    {
        $admin = Admin::factory()->suspended()->create(['role' => 'admin']);

        $this->fakeAdminSession('still-alive-device', $admin->id);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.admin.unsuspend', $admin->id));

        $this->assertDatabaseHas('admins', ['id' => $admin->id, 'is_active' => 1]);
        $this->assertDatabaseHas('sessions', ['id' => 'still-alive-device']);
    }

    #[Test]
    public function superadmin_can_verify_and_unverify_an_admin(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.admin.verify', $admin->id))
            ->assertRedirect();

        $this->assertDatabaseHas('admins', ['id' => $admin->id, 'is_verified' => 1]);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.admin.unverify', $admin->id))
            ->assertRedirect();

        $this->assertDatabaseHas('admins', ['id' => $admin->id, 'is_verified' => 0]);
    }

    #[Test]
    public function deleting_an_admin_with_jobs_is_blocked(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        Job::factory()->create(['admin_id' => $admin->id]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->delete(route('superadmin.admin.delete', $admin->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('admins', ['id' => $admin->id]);
    }

    #[Test]
    public function deleting_an_admin_with_no_jobs_or_applications_succeeds(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->delete(route('superadmin.admin.delete', $admin->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('admins', ['id' => $admin->id]);
    }

    #[Test]
    public function regular_admin_cannot_suspend_another_admin(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        $target = Admin::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin, 'admin')
            ->patch(route('superadmin.admin.suspend', $target->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('admins', ['id' => $target->id, 'is_active' => 1]);
    }
}
