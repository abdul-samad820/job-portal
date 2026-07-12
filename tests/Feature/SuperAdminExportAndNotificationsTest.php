<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuperAdminExportAndNotificationsTest extends TestCase
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
    public function jobs_csv_export_streams_a_csv_response(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        Job::factory()->count(3)->create(['admin_id' => $admin->id]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.jobs.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    #[Test]
    public function jobs_csv_export_can_filter_to_hidden_jobs(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        Job::factory()->create(['admin_id' => $admin->id, 'title' => 'Visible', 'is_hidden' => false]);
        Job::factory()->create(['admin_id' => $admin->id, 'title' => 'Hidden', 'is_hidden' => true]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.jobs.export', ['status' => 'hidden']));

        $response->assertStatus(200);
        $content = $response->streamedContent();

        $this->assertStringContainsString('Hidden', $content);
        $this->assertStringNotContainsString('Visible', $content);
    }

    /**
     * The CSV cell sanitizer prefixes a leading apostrophe onto any value
     * starting with =, +, -, or @ to prevent formula injection when the
     * export is opened in Excel/Sheets. A job title crafted to look like
     * a formula should come out neutralized.
     */
    #[Test]
    public function jobs_csv_export_neutralizes_formula_injection_in_titles(): void
    {
        $admin = Admin::factory()->create(['role' => 'admin']);
        Job::factory()->create(['admin_id' => $admin->id, 'title' => '=HYPERLINK("http://evil.com")']);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.jobs.export'));

        $content = $response->streamedContent();

        $this->assertStringContainsString("'=HYPERLINK", $content);
    }

    #[Test]
    public function superadmin_can_mark_their_notifications_as_read(): void
    {
        // PlatformAnnouncementNotification with sendEmail=false only uses
        // the 'database' channel, which is what populates unreadNotifications().
        $this->superAdmin->notify(
            new \App\Notifications\PlatformAnnouncementNotification('Heads up', 'Body text', false)
        );

        $this->assertEquals(1, $this->superAdmin->unreadNotifications()->count());

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.notifications.read'));

        $response->assertRedirect();
        $this->assertEquals(0, $this->superAdmin->unreadNotifications()->count());
    }

    #[Test]
    public function regular_admin_cannot_export_jobs_via_superadmin_route(): void
    {
        $response = $this->get(route('superadmin.jobs.export'));

        $response->assertRedirect(route('superadmin.login.view'));
    }
}
