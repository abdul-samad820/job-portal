<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\SavedJob;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Covers UserController's job-browsing and saved-jobs surface:
 * dashboard stats, job listing/search/filter/recommended, single job
 * view + view tracking, saved jobs (save/unsave + tier limit), and the
 * applied-jobs list with search/status filters.
 */
class UserJobBrowsingTest extends TestCase
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
    public function dashboard_renders_with_expected_counts(): void
    {
        Job::factory()->count(2)->create(['admin_id' => $this->admin->id]);
        $appliedJob = Job::factory()->create(['admin_id' => $this->admin->id]);
        JobApplication::factory()->create(['user_id' => $this->user->id, 'job_id' => $appliedJob->id]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('appliedJobsCount', 1);
        $response->assertViewHas('totalJobs', 3);
    }

    #[Test]
    public function job_listing_only_shows_visible_non_hidden_jobs(): void
    {
        Job::factory()->create(['admin_id' => $this->admin->id, 'title' => 'Visible Job', 'is_hidden' => false]);
        Job::factory()->create(['admin_id' => $this->admin->id, 'title' => 'Hidden Job', 'is_hidden' => true]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.jobs'));

        $jobs = $response->viewData('jobs');
        $titles = collect($jobs->items())->pluck('title');

        $this->assertTrue($titles->contains('Visible Job'));
        $this->assertFalse($titles->contains('Hidden Job'));
    }

    #[Test]
    public function job_listing_can_be_searched_by_title(): void
    {
        Job::factory()->create(['admin_id' => $this->admin->id, 'title' => 'Laravel Developer']);
        Job::factory()->create(['admin_id' => $this->admin->id, 'title' => 'Graphic Designer']);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.jobs', ['search' => 'Laravel']));

        $jobs = $response->viewData('jobs');
        $this->assertCount(1, $jobs);
        $this->assertEquals('Laravel Developer', $jobs->first()->title);
    }

    #[Test]
    public function job_listing_can_filter_by_category_and_location(): void
    {
        $category = JobCategory::factory()->create(['admin_id' => $this->admin->id]);
        Job::factory()->create(['admin_id' => $this->admin->id, 'category_id' => $category->id, 'location' => 'Mumbai']);
        Job::factory()->create(['admin_id' => $this->admin->id, 'location' => 'Delhi']);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.jobs', ['category' => $category->id]));

        $jobs = $response->viewData('jobs');
        $this->assertCount(1, $jobs);

        $locationResponse = $this->actingAs($this->user, 'user')
            ->get(route('user.jobs', ['location' => 'Mumbai']));

        $this->assertCount(1, $locationResponse->viewData('jobs'));
    }

    #[Test]
    public function recommended_jobs_match_against_users_core_skills(): void
    {
        UserProfile::create(['user_id' => $this->user->id, 'core_skills' => 'Laravel, PHP']);

        Job::factory()->create(['admin_id' => $this->admin->id, 'title' => 'Backend Dev', 'required_skills' => 'Laravel, MySQL']);
        Job::factory()->create(['admin_id' => $this->admin->id, 'title' => 'Photographer', 'required_skills' => 'Photoshop']);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.jobs', ['recommended' => 1]));

        $jobs = $response->viewData('jobs');
        $this->assertCount(1, $jobs);
        $this->assertEquals('Backend Dev', $jobs->first()->title);
        $this->assertFalse($response->viewData('noProfileSkills'));
    }

    #[Test]
    public function recommended_jobs_show_nothing_when_user_has_no_skills_on_file(): void
    {
        Job::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.jobs', ['recommended' => 1]));

        $this->assertCount(0, $response->viewData('jobs'));
        $this->assertTrue($response->viewData('noProfileSkills'));
    }

    #[Test]
    public function single_job_page_hides_a_job_hidden_by_moderation(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id, 'is_hidden' => true]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.job_single', $job->id));

        $response->assertStatus(404);
    }

    #[Test]
    public function viewing_a_single_job_increments_its_view_count_once_per_session(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id, 'views_count' => 0]);

        $this->actingAs($this->user, 'user')->get(route('user.job_single', $job->id));
        $this->assertEquals(1, $job->fresh()->views_count);

        // Same session viewing again shouldn't double-count.
        $this->actingAs($this->user, 'user')->get(route('user.job_single', $job->id));
        $this->assertEquals(1, $job->fresh()->views_count);
    }

    #[Test]
    public function user_can_save_and_unsave_a_job(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);

        $this->actingAs($this->user, 'user')
            ->post(route('saved.store', $job->id))
            ->assertRedirect();

        $this->assertDatabaseHas('saved_jobs', ['user_id' => $this->user->id, 'job_id' => $job->id]);

        $this->actingAs($this->user, 'user')
            ->delete(route('saved.destroy', $job->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('saved_jobs', ['user_id' => $this->user->id, 'job_id' => $job->id]);
    }

    #[Test]
    public function saving_the_same_job_twice_does_not_create_a_duplicate_row(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);

        $this->actingAs($this->user, 'user')->post(route('saved.store', $job->id));
        $this->actingAs($this->user, 'user')->post(route('saved.store', $job->id));

        $this->assertEquals(1, SavedJob::where('user_id', $this->user->id)->where('job_id', $job->id)->count());
    }

    #[Test]
    public function saving_a_job_beyond_the_base_tier_limit_of_ten_is_rejected(): void
    {
        $jobs = Job::factory()->count(10)->create(['admin_id' => $this->admin->id]);
        foreach ($jobs as $job) {
            SavedJob::create(['user_id' => $this->user->id, 'job_id' => $job->id]);
        }

        $eleventhJob = Job::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->user, 'user')
            ->post(route('saved.store', $eleventhJob->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('saved_jobs', ['user_id' => $this->user->id, 'job_id' => $eleventhJob->id]);
    }

    #[Test]
    public function saved_jobs_page_lists_only_the_current_users_saved_jobs(): void
    {
        $otherUser = User::factory()->create();
        $myJob = Job::factory()->create(['admin_id' => $this->admin->id]);
        $otherJob = Job::factory()->create(['admin_id' => $this->admin->id]);

        SavedJob::create(['user_id' => $this->user->id, 'job_id' => $myJob->id]);
        SavedJob::create(['user_id' => $otherUser->id, 'job_id' => $otherJob->id]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.saved.jobs'));

        $savedJobs = $response->viewData('savedJobs');
        $this->assertCount(1, $savedJobs);
        $this->assertEquals($myJob->id, $savedJobs->first()->job_id);
    }

    #[Test]
    public function applied_jobs_list_can_be_searched_and_filtered_by_status(): void
    {
        $jobA = Job::factory()->create(['admin_id' => $this->admin->id, 'title' => 'Frontend Role']);
        $jobB = Job::factory()->create(['admin_id' => $this->admin->id, 'title' => 'Backend Role']);

        JobApplication::factory()->create(['user_id' => $this->user->id, 'job_id' => $jobA->id, 'status' => 'pending']);
        JobApplication::factory()->create(['user_id' => $this->user->id, 'job_id' => $jobB->id, 'status' => 'shortlisted']);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.job_applied', ['search' => 'Frontend']));

        $this->assertCount(1, $response->viewData('applications'));

        $statusResponse = $this->actingAs($this->user, 'user')
            ->get(route('user.job_applied', ['status' => 'shortlisted']));

        $this->assertCount(1, $statusResponse->viewData('applications'));
    }

    #[Test]
    public function applied_jobs_list_only_shows_the_current_users_applications(): void
    {
        $otherUser = User::factory()->create();
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);

        JobApplication::factory()->create(['user_id' => $this->user->id, 'job_id' => $job->id]);
        JobApplication::factory()->create(['user_id' => $otherUser->id, 'job_id' => $job->id]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.job_applied'));

        $this->assertCount(1, $response->viewData('applications'));
    }

    #[Test]
    public function guest_cannot_access_the_dashboard_or_saved_jobs(): void
    {
        $this->get(route('user.dashboard'))->assertRedirect(route('user.login'));
        $this->get(route('user.saved.jobs'))->assertRedirect(route('user.login'));
    }
}
