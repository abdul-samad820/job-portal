<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Resume;
use App\Models\SavedJob;
use App\Models\User;
use App\Models\UserProfile;
use App\Notifications\NewJobApplicationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\CreatesFakeUploads;
use Tests\TestCase;

class BulkApplyTest extends TestCase
{
    use CreatesFakeUploads, RefreshDatabase;

    private User $user;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = Admin::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['email_verified_at' => now()]);

        UserProfile::create([
            'user_id' => $this->user->id,
            'professional_summary' => 'Experienced developer',
            'core_skills' => 'PHP, Laravel',
            'education' => [['degree' => 'BCA', 'institute' => 'XYZ', 'year' => '2023']],
        ]);
    }

    #[Test]
    public function form_redirects_when_no_jobs_were_selected(): void
    {
        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.bulk_apply.form'));

        $response->assertRedirect(route('user.jobs'));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function form_excludes_jobs_already_applied_to_or_expired(): void
    {
        $freshJob = Job::factory()->create(['admin_id' => $this->admin->id]);
        $expiredJob = Job::factory()->expired()->create(['admin_id' => $this->admin->id]);
        $appliedJob = Job::factory()->create(['admin_id' => $this->admin->id]);
        JobApplication::factory()->create(['user_id' => $this->user->id, 'job_id' => $appliedJob->id]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.bulk_apply.form', [
                'job_ids' => [$freshJob->id, $expiredJob->id, $appliedJob->id],
            ]));

        $response->assertStatus(200);
        $jobs = $response->viewData('jobs');
        $this->assertCount(1, $jobs);
        $this->assertEquals($freshJob->id, $jobs->first()->id);
    }

    #[Test]
    public function form_redirects_when_every_selected_job_is_unavailable(): void
    {
        $expiredJob = Job::factory()->expired()->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.bulk_apply.form', ['job_ids' => [$expiredJob->id]]));

        $response->assertRedirect(route('user.jobs'));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function user_can_bulk_apply_to_multiple_jobs_at_once_using_an_upload(): void
    {
        Notification::fake();

        $jobs = Job::factory()->count(3)->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.bulk_apply.store'), [
                'job_ids' => $jobs->pluck('id')->toArray(),
                'cover_letter' => 'I am excited to apply to all of these roles.',
                'resume_source' => 'upload',
                'resume' => $this->fakePdf(),
            ]);

        $response->assertRedirect(route('user.job_applied'));
        $response->assertSessionHas('success');

        foreach ($jobs as $job) {
            $this->assertDatabaseHas('job_applications', [
                'user_id' => $this->user->id,
                'job_id' => $job->id,
                'status' => 'pending',
            ]);
            Notification::assertSentTo($this->admin, NewJobApplicationNotification::class);
        }
    }

    #[Test]
    public function bulk_apply_can_use_a_resume_from_the_library(): void
    {
        $resume = Resume::create(['user_id' => $this->user->id, 'title' => 'My Resume', 'file_path' => 'resumes/mine.pdf']);
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);

        $this->actingAs($this->user, 'user')
            ->post(route('user.bulk_apply.store'), [
                'job_ids' => [$job->id],
                'cover_letter' => 'Applying with my saved resume.',
                'resume_source' => 'library',
                'resume_id' => $resume->id,
            ]);

        $this->assertDatabaseHas('job_applications', [
            'job_id' => $job->id,
            'resume' => 'resumes/mine.pdf',
            'resume_id' => $resume->id,
        ]);
    }

    #[Test]
    public function bulk_apply_skips_jobs_that_are_already_applied_to_or_expired(): void
    {
        $goodJob = Job::factory()->create(['admin_id' => $this->admin->id]);
        $expiredJob = Job::factory()->expired()->create(['admin_id' => $this->admin->id]);
        $alreadyAppliedJob = Job::factory()->create(['admin_id' => $this->admin->id]);
        JobApplication::factory()->create(['user_id' => $this->user->id, 'job_id' => $alreadyAppliedJob->id]);

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.bulk_apply.store'), [
                'job_ids' => [$goodJob->id, $expiredJob->id, $alreadyAppliedJob->id],
                'cover_letter' => 'Applying to whichever jobs are valid.',
                'resume_source' => 'upload',
                'resume' => $this->fakePdf(),
            ]);

        $response->assertSessionHas('success', 'Applied to 1 job(s) successfully. 2 job(s) were skipped (already applied, expired, or no longer available).');

        $this->assertEquals(2, JobApplication::where('user_id', $this->user->id)->count()); // goodJob + preexisting alreadyAppliedJob
    }

    #[Test]
    public function bulk_apply_removes_applied_jobs_from_saved_jobs(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);
        SavedJob::create(['user_id' => $this->user->id, 'job_id' => $job->id]);

        $this->actingAs($this->user, 'user')
            ->post(route('user.bulk_apply.store'), [
                'job_ids' => [$job->id],
                'cover_letter' => 'Applying to my saved job.',
                'resume_source' => 'upload',
                'resume' => $this->fakePdf(),
            ]);

        $this->assertDatabaseMissing('saved_jobs', ['user_id' => $this->user->id, 'job_id' => $job->id]);
    }

    #[Test]
    public function bulk_apply_requires_a_complete_profile(): void
    {
        $incompleteUser = User::factory()->create(['email_verified_at' => now()]);
        // no UserProfile created for this user at all
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($incompleteUser, 'user')
            ->post(route('user.bulk_apply.store'), [
                'job_ids' => [$job->id],
                'cover_letter' => 'Trying to apply without a profile.',
                'resume_source' => 'upload',
                'resume' => $this->fakePdf(),
            ]);

        $response->assertRedirect(route('user.profile'));
        $this->assertDatabaseMissing('job_applications', ['user_id' => $incompleteUser->id]);
    }

    #[Test]
    public function bulk_apply_requires_a_cover_letter_of_minimum_length(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.bulk_apply.store'), [
                'job_ids' => [$job->id],
                'cover_letter' => 'Too short',
                'resume_source' => 'upload',
                'resume' => $this->fakePdf(),
            ]);

        $response->assertSessionHasErrors('cover_letter');
    }

    #[Test]
    public function guest_cannot_bulk_apply(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);

        $this->post(route('user.bulk_apply.store'), ['job_ids' => [$job->id]])
            ->assertRedirect(route('user.login'));
    }
}
