<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\UserProfile;          
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Admin $admin;

    private Job $job;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Mail::fake(); // Prevent real emails during tests

        $this->admin = Admin::factory()->create();
        $this->user = User::factory()->create();
        $this->job = Job::factory()->create([
            'admin_id' => $this->admin->id,
            'last_date' => now()->addDays(30),
        ]);

       
        UserProfile::create([
            'user_id' => $this->user->id,
            'professional_summary' => 'Experienced Laravel developer',
            'core_skills' => 'PHP, Laravel, MySQL',
            'education' => [                     
                ['degree' => 'BCA', 'institute' => 'XYZ College', 'year' => '2023'],
            ],
        ]);
    }

   #[Test]
    public function user_can_apply_for_a_job(): void
    {
        $resume = UploadedFile::fake()->create('resume.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user, 'user')
            ->post(route('apply_job_application', $this->job->id), [
                'cover_letter' => 'I am very interested in this position and have 3 years experience.',
                'resume' => $resume,
            ]);

        $this->assertDatabaseHas('job_applications', [
            'user_id' => $this->user->id,
            'job_id' => $this->job->id,
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('user.jobs'));
        $response->assertSessionHas('success');
    }

   #[Test]
    public function user_cannot_apply_twice_for_same_job(): void
    {
        // Use factory instead of manual create
        JobApplication::factory()->create([
            'user_id' => $this->user->id,
            'job_id' => $this->job->id,
        ]);

        $resume = UploadedFile::fake()->create('resume.pdf', 500, 'application/pdf');
        $response = $this->actingAs($this->user, 'user')
            ->post(route('apply_job_application', $this->job->id), [
                'cover_letter' => 'Second attempt cover letter text here.',
                'resume' => $resume,
            ]);

        $response->assertSessionHas('error');

        // DB constraint check: exactly 1 record
        $this->assertDatabaseCount('job_applications', 1);
    }

   #[Test]
    public function user_cannot_apply_for_expired_job(): void
    {
        $expiredJob = Job::factory()->expired()->create([
            'admin_id' => $this->admin->id,
        ]);

        $resume = UploadedFile::fake()->create('resume.pdf', 500, 'application/pdf');
        $response = $this->actingAs($this->user, 'user')
            ->post(route('apply_job_application', $expiredJob->id), [
                'cover_letter' => 'Please consider my application.',
                'resume' => $resume,
            ]);

        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('job_applications', [
            'user_id' => $this->user->id,
            'job_id' => $expiredJob->id,
        ]);
    }

   #[Test]
    public function user_cannot_apply_without_complete_profile(): void
    {
        // User with NO profile
        $incompleteUser = User::factory()->create();

        $resume = UploadedFile::fake()->create('resume.pdf', 500, 'application/pdf');
        $response = $this->actingAs($incompleteUser, 'user')
            ->post(route('apply_job_application', $this->job->id), [
                'cover_letter' => 'I want to apply for this job.',
                'resume' => $resume,
            ]);

        // Redirected to profile page
        $response->assertRedirect(route('user.profile'));
        $response->assertSessionHas('error');
    }

   #[Test]
    public function admin_can_update_application_status(): void
    {
        $application = JobApplication::factory()->create([
            'user_id' => $this->user->id,
            'job_id' => $this->job->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.application.updateStatus', $application->id), [
                'status' => 'shortlisted',
            ]);

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => 'shortlisted',
        ]);

        $response->assertSessionHas('success');
    }

   #[Test]
    public function admin_cannot_update_another_admins_application(): void
    {
        $otherAdmin = Admin::factory()->create();
        $otherJob = Job::factory()->create(['admin_id' => $otherAdmin->id]);
        $application = JobApplication::factory()->create([
            'user_id' => $this->user->id,
            'job_id' => $otherJob->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.application.updateStatus', $application->id), [
                'status' => 'hired',
            ]);

        // Our admin cannot update another admin's application
        $response->assertStatus(403);
    }
}
