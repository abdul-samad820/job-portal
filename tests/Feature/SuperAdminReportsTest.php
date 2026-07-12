<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuperAdminReportsTest extends TestCase
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
    public function reports_page_renders_with_summary_totals(): void
    {
        Admin::factory()->count(2)->create(['role' => 'admin']);
        User::factory()->count(4)->create();

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.reports'));

        $response->assertStatus(200);
        $summary = $response->viewData('summary');

        $this->assertEquals(4, $summary['total_users']);
        $this->assertEquals(2, $summary['total_companies']);
    }

    #[Test]
    public function reports_trend_series_covers_every_day_in_the_window_including_zero_days(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        Job::factory()->create(['admin_id' => $admin->id, 'created_at' => now()]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.reports'));

        $jobsTrend = $response->viewData('jobsTrend');

        // 30-day window means 30 entries, even on days with zero jobs created.
        $this->assertCount(30, $jobsTrend);
        $this->assertEquals(1, $jobsTrend->get(now()->format('Y-m-d')));
    }

    #[Test]
    public function reports_top_categories_are_ordered_by_job_count(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);

        $popular = JobCategory::factory()->create(['name' => 'IT', 'admin_id' => $admin->id]);
        $quiet = JobCategory::factory()->create(['name' => 'Design', 'admin_id' => $admin->id]);

        Job::factory()->count(3)->create(['admin_id' => $admin->id, 'category_id' => $popular->id]);
        Job::factory()->count(1)->create(['admin_id' => $admin->id, 'category_id' => $quiet->id]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.reports'));

        $topCategories = $response->viewData('topCategories');

        $this->assertEquals('IT', $topCategories->first()->name);
    }

    #[Test]
    public function reports_application_status_breakdown_groups_correctly(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create(['admin_id' => $admin->id]);

        JobApplication::factory()->count(2)->create(['job_id' => $job->id, 'status' => 'pending']);
        JobApplication::factory()->count(1)->create(['job_id' => $job->id, 'status' => 'shortlisted']);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.reports'));

        $breakdown = $response->viewData('applicationStatusBreakdown');

        $this->assertEquals(2, $breakdown->get('pending'));
        $this->assertEquals(1, $breakdown->get('shortlisted'));
    }

    #[Test]
    public function regular_admin_cannot_view_platform_reports(): void
    {
        $response = $this->get(route('superadmin.reports'));

        $response->assertRedirect(route('superadmin.login.view'));
    }
}
