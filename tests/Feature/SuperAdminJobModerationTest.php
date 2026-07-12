<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobReport;
use App\Models\User;
use App\Notifications\JobHiddenNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuperAdminJobModerationTest extends TestCase
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

    private function makeReport(Job $job, array $overrides = []): JobReport
    {
        return JobReport::create(array_merge([
            'job_id' => $job->id,
            'user_id' => User::factory()->create()->id,
            'reason' => 'fake',
            'details' => 'This looks like a scam.',
            'status' => JobReport::STATUS_PENDING,
        ], $overrides));
    }

    #[Test]
    public function job_index_lists_jobs_across_all_companies_with_report_counts(): void
    {
        $companyA = Admin::factory()->create(['role' => 'admin', 'company_name' => 'Alpha Co']);
        $companyB = Admin::factory()->create(['role' => 'admin', 'company_name' => 'Beta Co']);
        Job::factory()->create(['admin_id' => $companyA->id, 'title' => 'Alpha Job']);
        $reportedJob = Job::factory()->create(['admin_id' => $companyB->id, 'title' => 'Beta Job']);
        $this->makeReport($reportedJob);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.jobs'));

        $response->assertStatus(200);
        $jobs = $response->viewData('jobs');
        $this->assertCount(2, $jobs);
    }

    #[Test]
    public function job_index_can_filter_to_hidden_jobs_only(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        Job::factory()->create(['admin_id' => $admin->id, 'title' => 'Visible Job', 'is_hidden' => false]);
        Job::factory()->create(['admin_id' => $admin->id, 'title' => 'Hidden Job', 'is_hidden' => true]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.jobs', ['status' => 'hidden']));

        $jobs = $response->viewData('jobs');
        $this->assertCount(1, $jobs);
        $this->assertEquals('Hidden Job', $jobs->first()->title);
    }

    #[Test]
    public function job_index_can_filter_to_reported_jobs_only(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        $reported = Job::factory()->create(['admin_id' => $admin->id, 'title' => 'Reported Job']);
        Job::factory()->create(['admin_id' => $admin->id, 'title' => 'Clean Job']);
        $this->makeReport($reported);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.jobs', ['status' => 'reported']));

        $jobs = $response->viewData('jobs');
        $this->assertCount(1, $jobs);
        $this->assertEquals('Reported Job', $jobs->first()->title);
    }

    #[Test]
    public function superadmin_can_hide_a_job_with_a_reason_and_it_notifies_the_owning_admin(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create(['admin_id' => $admin->id, 'is_hidden' => false]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.jobs.hide', $job->id), [
                'hidden_reason' => 'Violates content policy.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'is_hidden' => 1,
            'hidden_reason' => 'Violates content policy.',
        ]);

        Notification::assertSentTo($admin, JobHiddenNotification::class);
    }

    #[Test]
    public function hiding_a_job_requires_a_reason(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create(['admin_id' => $admin->id]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.jobs.hide', $job->id), [
                'hidden_reason' => '',
            ]);

        $response->assertSessionHasErrors('hidden_reason');
        $this->assertDatabaseHas('jobs', ['id' => $job->id, 'is_hidden' => 0]);
    }

    #[Test]
    public function superadmin_can_unhide_a_job_and_it_clears_the_reason(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create([
            'admin_id' => $admin->id,
            'is_hidden' => true,
            'hidden_reason' => 'Old reason',
        ]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.jobs.unhide', $job->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('jobs', ['id' => $job->id, 'is_hidden' => 0, 'hidden_reason' => null]);
    }

    #[Test]
    public function deleting_a_job_with_applications_requires_explicit_confirmation(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create(['admin_id' => $admin->id]);
        JobApplication::factory()->create(['job_id' => $job->id]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->delete(route('superadmin.jobs.destroy', $job->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('jobs', ['id' => $job->id]);
    }

    #[Test]
    public function deleting_a_job_with_applications_succeeds_once_confirmed(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create(['admin_id' => $admin->id]);
        JobApplication::factory()->create(['job_id' => $job->id]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->delete(route('superadmin.jobs.destroy', $job->id).'?confirm_delete=1');

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }

    #[Test]
    public function deleting_a_job_with_no_applications_needs_no_confirmation(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create(['admin_id' => $admin->id]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->delete(route('superadmin.jobs.destroy', $job->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }

    #[Test]
    public function job_reports_queue_defaults_to_pending_filter(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create(['admin_id' => $admin->id]);
        $this->makeReport($job, ['status' => JobReport::STATUS_PENDING]);
        $this->makeReport($job, ['status' => JobReport::STATUS_DISMISSED]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.job-reports'));

        $reports = $response->viewData('reports');
        $this->assertCount(1, $reports);
        $this->assertEquals(JobReport::STATUS_PENDING, $reports->first()->status);
    }

    #[Test]
    public function superadmin_can_dismiss_a_report(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create(['admin_id' => $admin->id]);
        $report = $this->makeReport($job);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.job-reports.dismiss', $report->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('job_reports', ['id' => $report->id, 'status' => JobReport::STATUS_DISMISSED]);
    }

    #[Test]
    public function actioning_a_report_hides_the_job_and_marks_all_pending_reports_on_it_reviewed(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create(['admin_id' => $admin->id, 'is_hidden' => false]);
        $report1 = $this->makeReport($job);
        $report2 = $this->makeReport($job);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.job-reports.action', $report1->id), [
                'hidden_reason' => 'Confirmed fraudulent listing.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jobs', ['id' => $job->id, 'is_hidden' => 1]);
        $this->assertDatabaseHas('job_reports', ['id' => $report1->id, 'status' => JobReport::STATUS_REVIEWED]);
        $this->assertDatabaseHas('job_reports', ['id' => $report2->id, 'status' => JobReport::STATUS_REVIEWED]);
        Notification::assertSentTo($admin, JobHiddenNotification::class);
    }

    #[Test]
    public function regular_admin_cannot_moderate_jobs_via_superadmin_routes(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create(['admin_id' => $admin->id]);

        $response = $this->patch(route('superadmin.jobs.hide', $job->id), [
            'hidden_reason' => 'test',
        ]);

        $response->assertRedirect(route('superadmin.login.view'));
    }
}
