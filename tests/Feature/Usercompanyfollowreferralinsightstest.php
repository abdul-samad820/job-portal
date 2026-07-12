<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\CompanyFollow;
use App\Models\Job;
use App\Models\JobRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserCompanyFollowReferralInsightsTest extends TestCase
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

    // ---------------------------------------------------------------
    // Company Follow
    // ---------------------------------------------------------------

    #[Test]
    public function user_can_follow_and_unfollow_a_company(): void
    {
        $this->actingAs($this->user, 'user')
            ->post(route('user.following.toggle', $this->admin->id))
            ->assertRedirect();

        $this->assertDatabaseHas('company_follows', ['user_id' => $this->user->id, 'admin_id' => $this->admin->id]);

        $this->actingAs($this->user, 'user')
            ->post(route('user.following.toggle', $this->admin->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('company_follows', ['user_id' => $this->user->id, 'admin_id' => $this->admin->id]);
    }

    #[Test]
    public function following_more_than_five_companies_beyond_base_tier_is_rejected(): void
    {
        $admins = Admin::factory()->count(5)->create(['role' => 'admin']);
        foreach ($admins as $admin) {
            CompanyFollow::create(['user_id' => $this->user->id, 'admin_id' => $admin->id]);
        }

        $sixthAdmin = Admin::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.following.toggle', $sixthAdmin->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('company_follows', ['user_id' => $this->user->id, 'admin_id' => $sixthAdmin->id]);
    }

    #[Test]
    public function following_index_lists_only_this_users_follows(): void
    {
        $otherUser = User::factory()->create();
        CompanyFollow::create(['user_id' => $this->user->id, 'admin_id' => $this->admin->id]);
        CompanyFollow::create(['user_id' => $otherUser->id, 'admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.following.index'));

        $response->assertStatus(200);
        $this->assertCount(1, $response->viewData('follows'));
    }

    // ---------------------------------------------------------------
    // Referrals
    // ---------------------------------------------------------------

    #[Test]
    public function referral_page_lists_users_referred_by_me(): void
    {
        User::factory()->count(3)->create(['referred_by' => $this->user->id]);
        User::factory()->create(); // unrelated user

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.referrals'));

        $response->assertStatus(200);
        $this->assertCount(3, $response->viewData('referredUsers'));
        $this->assertEquals(3, $response->viewData('count'));
    }

    #[Test]
    public function referral_page_awards_bronze_badge_at_ten_referrals(): void
    {
        User::factory()->count(10)->create(['referred_by' => $this->user->id]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.referrals'));

        $badge = $response->viewData('earnedBadge');
        $this->assertEquals('Bronze Referrer', $badge['label']);
    }

    #[Test]
    public function referral_page_shows_no_badge_below_ten_referrals(): void
    {
        User::factory()->count(3)->create(['referred_by' => $this->user->id]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.referrals'));

        $this->assertNull($response->viewData('earnedBadge'));
        $this->assertEquals(10, $response->viewData('nextMilestone')['threshold']);
    }

    // ---------------------------------------------------------------
    // Salary Insights
    // ---------------------------------------------------------------

    #[Test]
    public function salary_insights_computes_stats_for_a_role(): void
    {
        $role = JobRole::factory()->create();
        Job::factory()->create(['role_id' => $role->id, 'admin_id' => $this->admin->id, 'min_salary' => 400000, 'max_salary' => 800000]);
        Job::factory()->create(['role_id' => $role->id, 'admin_id' => $this->admin->id, 'min_salary' => 600000, 'max_salary' => 1000000]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.salary_insights', ['role_id' => $role->id]));

        $response->assertStatus(200);
        $stats = $response->viewData('stats');
        $this->assertEquals(2, $stats['job_count']);
        $this->assertEquals(500000, $stats['avg_min']);
    }

    #[Test]
    public function salary_insights_returns_no_stats_without_a_role_selected(): void
    {
        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.salary_insights'));

        $response->assertStatus(200);
        $this->assertNull($response->viewData('stats'));
    }

    #[Test]
    public function salary_insights_enforces_the_daily_lookup_cap(): void
    {
        $role = JobRole::factory()->create();
        Job::factory()->create(['role_id' => $role->id, 'admin_id' => $this->admin->id, 'min_salary' => 100, 'max_salary' => 200]);

        // Base tier daily cap is 5 — the 6th lookup today should be blocked.
        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($this->user, 'user')
                ->get(route('user.salary_insights', ['role_id' => $role->id]));
        }

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.salary_insights', ['role_id' => $role->id]));

        $response->assertSessionHas('error');
        $this->assertNull($response->viewData('stats'));
    }

    // ---------------------------------------------------------------
    // Job Compare
    // ---------------------------------------------------------------

    #[Test]
    public function compare_page_shows_the_selected_visible_jobs(): void
    {
        $jobs = Job::factory()->count(2)->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.compare', ['job_ids' => $jobs->pluck('id')->toArray()]));

        $response->assertStatus(200);
        $this->assertCount(2, $response->viewData('jobs'));
    }

    #[Test]
    public function compare_page_caps_selection_at_three_jobs(): void
    {
        $jobs = Job::factory()->count(5)->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.compare', ['job_ids' => $jobs->pluck('id')->toArray()]));

        $this->assertCount(3, $response->viewData('jobs'));
    }

    #[Test]
    public function compare_page_excludes_hidden_jobs(): void
    {
        $visible = Job::factory()->create(['admin_id' => $this->admin->id, 'is_hidden' => false]);
        $hidden = Job::factory()->create(['admin_id' => $this->admin->id, 'is_hidden' => true]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.compare', ['job_ids' => [$visible->id, $hidden->id]]));

        $jobs = $response->viewData('jobs');
        $this->assertCount(1, $jobs);
        $this->assertEquals($visible->id, $jobs->first()->id);
    }

    #[Test]
    public function guest_cannot_access_following_referrals_or_insights(): void
    {
        $this->get(route('user.following.index'))->assertRedirect(route('user.login'));
        $this->get(route('user.referrals'))->assertRedirect(route('user.login'));
        $this->get(route('user.salary_insights'))->assertRedirect(route('user.login'));
    }
}
