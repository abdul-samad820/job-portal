<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Notifications\PlatformAnnouncementNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuperAdminBroadcastTest extends TestCase
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
    public function broadcast_form_renders(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.broadcast'));

        $response->assertStatus(200);
    }

    #[Test]
    public function broadcast_to_admins_only_notifies_admins_not_users(): void
    {
        Notification::fake();

        $admins = Admin::factory()->count(2)->create(['role' => 'admin']);
        $users = User::factory()->count(2)->create();

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.broadcast.send'), [
                'title' => 'Maintenance Notice',
                'message' => 'We will be down for maintenance tonight.',
                'audience' => 'admins',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        foreach ($admins as $admin) {
            Notification::assertSentTo($admin, PlatformAnnouncementNotification::class);
        }
        foreach ($users as $user) {
            Notification::assertNotSentTo($user, PlatformAnnouncementNotification::class);
        }
    }

    #[Test]
    public function broadcast_to_users_only_notifies_users_not_admins(): void
    {
        Notification::fake();

        $admins = Admin::factory()->count(2)->create(['role' => 'admin']);
        $users = User::factory()->count(2)->create();

        $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.broadcast.send'), [
                'title' => 'New Feature',
                'message' => 'Check out resume library!',
                'audience' => 'users',
            ]);

        foreach ($users as $user) {
            Notification::assertSentTo($user, PlatformAnnouncementNotification::class);
        }
        foreach ($admins as $admin) {
            Notification::assertNotSentTo($admin, PlatformAnnouncementNotification::class);
        }
    }

    #[Test]
    public function broadcast_to_both_notifies_everyone(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.broadcast.send'), [
                'title' => 'Platform Update',
                'message' => 'Big changes coming.',
                'audience' => 'both',
            ]);

        Notification::assertSentTo($admin, PlatformAnnouncementNotification::class);
        Notification::assertSentTo($user, PlatformAnnouncementNotification::class);
    }

    #[Test]
    public function broadcast_requires_title_message_and_valid_audience(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.broadcast.send'), [
                'title' => '',
                'message' => '',
                'audience' => 'everyone', // not a valid enum value
            ]);

        $response->assertSessionHasErrors(['title', 'message', 'audience']);
    }

    #[Test]
    public function broadcast_logs_recipient_count_in_activity_log(): void
    {
        Admin::factory()->count(3)->create(['role' => 'admin']);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.broadcast.send'), [
                'title' => 'Announcement',
                'message' => 'Hello everyone.',
                'audience' => 'admins',
            ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'action' => 'announcement_broadcast',
        ]);
    }
}
