<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Job;
use App\Models\JobInvite;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Notifications\PlatformAnnouncementNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserActivityAndInvitesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    #[Test]
    public function login_and_logout_are_recorded_in_the_activity_log(): void
    {
        $this->post(route('user.login.submit'), [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('user_activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'login',
        ]);

        $this->actingAs($this->user, 'user')->post(route('user.logout'));

        $this->assertDatabaseHas('user_activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'logout',
        ]);
    }

    #[Test]
    public function activity_log_page_only_shows_the_current_users_own_entries(): void
    {
        $otherUser = User::factory()->create();

        UserActivityLog::log($this->user->id, 'login');
        UserActivityLog::log($otherUser->id, 'login');

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.activity_log'));

        $response->assertStatus(200);
        $logs = $response->viewData('logs');
        $this->assertCount(1, $logs);
        $this->assertEquals($this->user->id, $logs->first()->user_id);
    }

    #[Test]
    public function invites_page_lists_only_this_users_invites(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);
        $otherUser = User::factory()->create();

        JobInvite::create([
            'job_id' => $job->id, 'user_id' => $this->user->id,
            'admin_id' => $this->admin->id, 'status' => 'pending',
        ]);
        JobInvite::create([
            'job_id' => $job->id, 'user_id' => $otherUser->id,
            'admin_id' => $this->admin->id, 'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.invites'));

        $response->assertStatus(200);
        $invites = $response->viewData('invites');
        $this->assertCount(1, $invites);
    }

    #[Test]
    public function opening_the_invites_page_marks_pending_invites_as_viewed(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);
        $invite = JobInvite::create([
            'job_id' => $job->id, 'user_id' => $this->user->id,
            'admin_id' => $this->admin->id, 'status' => 'pending',
        ]);

        $this->actingAs($this->user, 'user')->get(route('user.invites'));

        $this->assertDatabaseHas('job_invites', ['id' => $invite->id, 'status' => 'viewed']);
    }

    #[Test]
    public function opening_invites_does_not_touch_already_actioned_invites(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);
        $invite = JobInvite::create([
            'job_id' => $job->id, 'user_id' => $this->user->id,
            'admin_id' => $this->admin->id, 'status' => 'applied',
        ]);

        $this->actingAs($this->user, 'user')->get(route('user.invites'));

        $this->assertDatabaseHas('job_invites', ['id' => $invite->id, 'status' => 'applied']);
    }

    #[Test]
    public function user_can_mark_their_notifications_as_read(): void
    {
        $this->user->notify(new PlatformAnnouncementNotification('Update', 'Body', false));
        $this->assertEquals(1, $this->user->unreadNotifications()->count());

        $this->actingAs($this->user, 'user')
            ->post(route('user.notifications.read'))
            ->assertRedirect();

        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    #[Test]
    public function guest_cannot_view_activity_log_or_invites(): void
    {
        $this->get(route('user.activity_log'))->assertRedirect(route('user.login'));
        $this->get(route('user.invites'))->assertRedirect(route('user.login'));
    }
}
