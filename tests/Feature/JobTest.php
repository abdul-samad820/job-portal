<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();
    }

    #[Test]
    public function admin_can_create_a_job(): void
    {
        $category = JobCategory::factory()->create(['admin_id' => $this->admin->id]);
        $role = JobRole::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.job_create'), [
                'title' => 'Laravel Developer',
                'description' => 'We need an experienced Laravel developer',
                'location' => 'Delhi',
                'min_salary' => 50000,
                'max_salary' => 100000,
                'type' => 'Full-time',
                'experience' => 'Fresher',
                'last_date' => now()->addDays(30)->format('Y-m-d'),
                'category_id' => $category->id,
                'role_id' => $role->id,
            ]);

        // DB mein job bana?
        $this->assertDatabaseHas('jobs', [
            'title' => 'Laravel Developer',
            'admin_id' => $this->admin->id,
        ]);

        $response->assertRedirect(route('admin.job'));
        $response->assertSessionHas('success');
    }

    #[Test]
    public function admin_cannot_create_job_with_missing_required_fields(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.job_create'), [
                'title' => '',
            ]);

        $response->assertSessionHasErrors('title');
    }

    #[Test]
    public function admin_can_update_their_own_job(): void
    {
        $category = JobCategory::factory()->create(['admin_id' => $this->admin->id]);
        $role = JobRole::factory()->create(['admin_id' => $this->admin->id]);
        $job = Job::factory()->create([
            'admin_id' => $this->admin->id,
            'category_id' => $category->id,
            'role_id' => $role->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.job_update', $job->id), [
                'title' => 'Senior Laravel Developer',
                'description' => 'Updated description with enough content here.',
                'overview' => 'Updated overview section content.',
                'responsibilities' => 'Updated responsibilities content.',
                'required_skills' => 'PHP, Laravel, MySQL',
                'location' => 'Mumbai',
                'type' => 'Full-time',
                'experience' => '2 Years',
                'last_date' => now()->addDays(30)->format('Y-m-d'),
                'category_id' => $job->category_id,
                'role_id' => $job->role_id,
                'min_salary' => 50000,
                'max_salary' => 100000,
            ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Senior Laravel Developer',
        ]);
    }

    #[Test]
    public function admin_cannot_update_another_admins_job(): void
    {
        $otherAdmin = Admin::factory()->create();
        $job = Job::factory()->create(['admin_id' => $otherAdmin->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.job_update', $job->id), [
                'title' => 'Hacked Title',
            ]);

        // 403 Forbidden
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_delete_their_own_job(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.job_delete', $job->id));

        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }

    #[Test]
    public function admin_cannot_delete_job_with_applications_without_confirmation(): void
    {
        $job = Job::factory()->create(['admin_id' => $this->admin->id]);
        \App\Models\JobApplication::factory()->create(['job_id' => $job->id]);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.job_delete', $job->id));

        $this->assertDatabaseHas('jobs', ['id' => $job->id]);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.job_delete', ['id' => $job->id, 'confirm_delete' => 1]));

        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }

    #[Test]
    public function admin_cannot_delete_category_with_linked_jobs(): void
    {
        $category = JobCategory::factory()->create(['admin_id' => $this->admin->id]);
        Job::factory()->create(['admin_id' => $this->admin->id, 'category_id' => $category->id]);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.job_category_delete', $category->id));

        $this->assertDatabaseHas('job_categories', ['id' => $category->id]);
    }

    #[Test]
    public function max_salary_must_be_greater_than_min_salary(): void
    {
        $category = JobCategory::factory()->create(['admin_id' => $this->admin->id]);
        $role = JobRole::factory()->create(['admin_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.job_create'), [
                'title' => 'Test Job',
                'description' => 'Test description here',
                'location' => 'Delhi',
                'min_salary' => 100000,
                'max_salary' => 50000,
                'type' => 'Full-time',
                'category_id' => $category->id,
                'role_id' => $role->id,
            ]);

        $response->assertSessionHasErrors('max_salary');
    }
}
