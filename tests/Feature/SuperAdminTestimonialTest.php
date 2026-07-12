<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuperAdminTestimonialTest extends TestCase
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

    private function makeTestimonial(array $overrides = []): Testimonial
    {
        return Testimonial::create(array_merge([
            'admin_id' => null,
            'name' => 'Jane Doe',
            'review' => 'Great platform!',
            'rating' => 5,
            'status' => Testimonial::STATUS_PENDING,
        ], $overrides));
    }

    #[Test]
    public function testimonial_index_lists_across_all_companies(): void
    {
        $company = Admin::factory()->create(['role' => 'admin']);

        $this->makeTestimonial(['admin_id' => null, 'name' => 'Global Reviewer']);
        $this->makeTestimonial(['admin_id' => $company->id, 'name' => 'Company Reviewer']);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.testimonials'));

        $response->assertStatus(200);
        $testimonials = $response->viewData('testimonials');
        $this->assertCount(2, $testimonials);
    }

    #[Test]
    public function testimonial_index_can_filter_by_status(): void
    {
        $this->makeTestimonial(['status' => Testimonial::STATUS_PENDING, 'name' => 'Pending One']);
        $this->makeTestimonial(['status' => Testimonial::STATUS_APPROVED, 'name' => 'Approved One']);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.testimonials', ['filter' => 'pending']));

        $testimonials = $response->viewData('testimonials');
        $this->assertCount(1, $testimonials);
        $this->assertEquals('Pending One', $testimonials->first()->name);
    }

    #[Test]
    public function pending_count_reflects_only_pending_testimonials(): void
    {
        $this->makeTestimonial(['status' => Testimonial::STATUS_PENDING]);
        $this->makeTestimonial(['status' => Testimonial::STATUS_PENDING]);
        $this->makeTestimonial(['status' => Testimonial::STATUS_APPROVED]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.testimonials'));

        $this->assertEquals(2, $response->viewData('pendingCount'));
    }

    #[Test]
    public function superadmin_can_approve_a_pending_testimonial(): void
    {
        $testimonial = $this->makeTestimonial(['status' => Testimonial::STATUS_PENDING]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.testimonials.approve', $testimonial->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('testimonials', ['id' => $testimonial->id, 'status' => Testimonial::STATUS_APPROVED]);
    }

    #[Test]
    public function superadmin_can_reject_a_pending_testimonial(): void
    {
        $testimonial = $this->makeTestimonial(['status' => Testimonial::STATUS_PENDING]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.testimonials.reject', $testimonial->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('testimonials', ['id' => $testimonial->id, 'status' => Testimonial::STATUS_REJECTED]);
    }

    #[Test]
    public function superadmin_can_permanently_delete_any_testimonial_regardless_of_owner(): void
    {
        $company = Admin::factory()->create(['role' => 'admin']);
        $testimonial = $this->makeTestimonial(['admin_id' => $company->id]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->delete(route('superadmin.testimonials.destroy', $testimonial->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('testimonials', ['id' => $testimonial->id]);
    }

    #[Test]
    public function regular_admin_cannot_moderate_testimonials_via_superadmin_routes(): void
    {
        $testimonial = $this->makeTestimonial();

        $response = $this->patch(route('superadmin.testimonials.approve', $testimonial->id));

        $response->assertRedirect(route('superadmin.login.view'));
    }
}
