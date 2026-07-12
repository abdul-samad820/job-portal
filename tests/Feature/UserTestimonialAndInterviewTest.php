<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Interview;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTestimonialAndInterviewTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Admin $admin;

    private Job $job;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = Admin::factory()->create(['role' => 'admin', 'company_name' => 'Acme Corp']);
        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->job = Job::factory()->create(['admin_id' => $this->admin->id]);
    }

    #[Test]
    public function write_review_form_is_only_reachable_for_a_hired_application(): void
    {
        $pendingApp = JobApplication::factory()->create([
            'user_id' => $this->user->id, 'job_id' => $this->job->id, 'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.review.create', $pendingApp->id));

        $response->assertStatus(404);
    }

    #[Test]
    public function write_review_form_renders_for_a_hired_application(): void
    {
        $hiredApp = JobApplication::factory()->hired()->create([
            'user_id' => $this->user->id, 'job_id' => $this->job->id,
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.review.create', $hiredApp->id));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_submit_a_review_for_their_hired_application(): void
    {
        $hiredApp = JobApplication::factory()->hired()->create([
            'user_id' => $this->user->id, 'job_id' => $this->job->id,
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.review.store', $hiredApp->id), [
                'designation' => 'Backend Developer',
                'review' => 'Great company, learned a lot!',
                'rating' => 5,
            ]);

        $response->assertRedirect(route('user.job_applied'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('testimonials', [
            'job_application_id' => $hiredApp->id,
            'user_id' => $this->user->id,
            'status' => Testimonial::STATUS_PENDING,
            'admin_id' => null,
            'company' => 'Acme Corp',
        ]);
    }

    #[Test]
    public function user_cannot_submit_a_review_for_someone_elses_application(): void
    {
        $otherUser = User::factory()->create();
        $hiredApp = JobApplication::factory()->hired()->create([
            'user_id' => $otherUser->id, 'job_id' => $this->job->id,
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.review.store', $hiredApp->id), [
                'review' => 'Trying to review someone else\'s job.',
                'rating' => 5,
            ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('testimonials', ['job_application_id' => $hiredApp->id]);
    }

    #[Test]
    public function user_cannot_submit_a_review_twice_for_the_same_application(): void
    {
        $hiredApp = JobApplication::factory()->hired()->create([
            'user_id' => $this->user->id, 'job_id' => $this->job->id,
        ]);

        $this->actingAs($this->user, 'user')
            ->post(route('user.review.store', $hiredApp->id), [
                'review' => 'First review.',
                'rating' => 4,
            ]);

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.review.store', $hiredApp->id), [
                'review' => 'Trying to submit again.',
                'rating' => 5,
            ]);

        $response->assertRedirect(route('user.job_applied'));
        $response->assertSessionHas('info');
        $this->assertEquals(1, Testimonial::where('job_application_id', $hiredApp->id)->count());
    }

    #[Test]
    public function review_submission_requires_review_text_and_a_valid_rating(): void
    {
        $hiredApp = JobApplication::factory()->hired()->create([
            'user_id' => $this->user->id, 'job_id' => $this->job->id,
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.review.store', $hiredApp->id), [
                'review' => '',
                'rating' => 8, // out of 1-5 range
            ]);

        $response->assertSessionHasErrors(['review', 'rating']);
    }

    #[Test]
    public function review_submission_accepts_an_optional_image(): void
    {
        $hiredApp = JobApplication::factory()->hired()->create([
            'user_id' => $this->user->id, 'job_id' => $this->job->id,
        ]);

        $this->actingAs($this->user, 'user')
            ->post(route('user.review.store', $hiredApp->id), [
                'review' => 'Loved working here.',
                'rating' => 5,
                'image' => UploadedFile::fake()->image('me.jpg'),
            ]);

        $testimonial = Testimonial::where('job_application_id', $hiredApp->id)->first();
        $this->assertNotNull($testimonial->image);
        Storage::disk('public')->assertExists($testimonial->image);
    }

    #[Test]
    public function my_interviews_page_splits_upcoming_and_past_interviews(): void
    {
        $application = JobApplication::factory()->create([
            'user_id' => $this->user->id, 'job_id' => $this->job->id,
        ]);

        $secondJob = Job::factory()->create(['admin_id' => $this->admin->id]);
        $secondApplication = JobApplication::factory()->create([
            'user_id' => $this->user->id, 'job_id' => $secondJob->id,
        ]);

        Interview::create([
            'job_application_id' => $application->id,
            'admin_id' => $this->admin->id,
            'interview_date' => now()->addDays(3)->toDateString(),
            'interview_time' => '10:00',
            'mode' => 'online',
            'status' => 'scheduled',
        ]);

        Interview::create([
            'job_application_id' => $secondApplication->id,
            'admin_id' => $this->admin->id,
            'interview_date' => now()->subDays(3)->toDateString(),
            'interview_time' => '11:00',
            'mode' => 'offline',
            'location' => 'Office',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.interviews'));

        $response->assertStatus(200);
        $this->assertCount(1, $response->viewData('upcoming'));
        $this->assertCount(1, $response->viewData('past'));
    }

    #[Test]
    public function my_interviews_page_only_shows_the_current_users_interviews(): void
    {
        $otherUser = User::factory()->create();
        $otherApp = JobApplication::factory()->create(['user_id' => $otherUser->id, 'job_id' => $this->job->id]);

        Interview::create([
            'job_application_id' => $otherApp->id,
            'admin_id' => $this->admin->id,
            'interview_date' => now()->addDays(1)->toDateString(),
            'interview_time' => '09:00',
            'mode' => 'online',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.interviews'));

        $this->assertCount(0, $response->viewData('upcoming'));
    }

    #[Test]
    public function guest_cannot_write_a_review_or_view_interviews(): void
    {
        $hiredApp = JobApplication::factory()->hired()->create([
            'user_id' => $this->user->id, 'job_id' => $this->job->id,
        ]);

        $this->get(route('user.review.create', $hiredApp->id))->assertRedirect(route('user.login'));
        $this->get(route('user.interviews'))->assertRedirect(route('user.login'));
    }
}
