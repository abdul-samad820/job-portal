<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuperAdminFaqTest extends TestCase
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
    public function faq_index_only_shows_global_faqs_not_company_specific_ones(): void
    {
        $companyAdmin = Admin::factory()->create(['role' => 'admin']);

        Faq::create(['admin_id' => null, 'question' => 'Global Q', 'answer' => 'Global A', 'status' => 1]);
        Faq::create(['admin_id' => $companyAdmin->id, 'question' => 'Company Q', 'answer' => 'Company A', 'status' => 1]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.faqs'));

        $faqs = $response->viewData('faqs');

        $this->assertCount(1, $faqs);
        $this->assertEquals('Global Q', $faqs->first()->question);
    }

    #[Test]
    public function superadmin_can_create_a_global_faq(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.faqs.store'), [
                'question' => 'How do I apply?',
                'answer' => 'Click the apply button on the job page.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('faqs', [
            'question' => 'How do I apply?',
            'admin_id' => null,
        ]);
    }

    #[Test]
    public function faq_creation_strips_html_tags_from_question_and_answer(): void
    {
        $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.faqs.store'), [
                'question' => '<script>alert(1)</script>Is this safe?',
                'answer' => '<b>Yes</b> it is.',
            ]);

        $this->assertDatabaseHas('faqs', [
            'question' => 'alert(1)Is this safe?',
            'answer' => 'Yes it is.',
        ]);
    }

    #[Test]
    public function faq_creation_requires_question_and_answer(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->post(route('superadmin.faqs.store'), [
                'question' => '',
                'answer' => '',
            ]);

        $response->assertSessionHasErrors(['question', 'answer']);
    }

    #[Test]
    public function superadmin_can_update_a_global_faq(): void
    {
        $faq = Faq::create(['admin_id' => null, 'question' => 'Old Q', 'answer' => 'Old A', 'status' => 1]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->put(route('superadmin.faqs.update', $faq->id), [
                'question' => 'New Q',
                'answer' => 'New A',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('faqs', ['id' => $faq->id, 'question' => 'New Q', 'answer' => 'New A']);
    }

    #[Test]
    public function superadmin_cannot_update_a_company_specific_faq_through_this_endpoint(): void
    {
        $companyAdmin = Admin::factory()->create(['role' => 'admin']);
        $faq = Faq::create(['admin_id' => $companyAdmin->id, 'question' => 'Q', 'answer' => 'A', 'status' => 1]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->put(route('superadmin.faqs.update', $faq->id), [
                'question' => 'Hijacked',
                'answer' => 'Hijacked',
            ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('faqs', ['id' => $faq->id, 'question' => 'Q']);
    }

    #[Test]
    public function superadmin_can_toggle_a_faqs_visibility(): void
    {
        $faq = Faq::create(['admin_id' => null, 'question' => 'Q', 'answer' => 'A', 'status' => 1]);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.faqs.toggle', $faq->id));

        $this->assertDatabaseHas('faqs', ['id' => $faq->id, 'status' => 0]);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->patch(route('superadmin.faqs.toggle', $faq->id));

        $this->assertDatabaseHas('faqs', ['id' => $faq->id, 'status' => 1]);
    }

    #[Test]
    public function superadmin_can_delete_a_global_faq(): void
    {
        $faq = Faq::create(['admin_id' => null, 'question' => 'Q', 'answer' => 'A', 'status' => 1]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->delete(route('superadmin.faqs.destroy', $faq->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }
}
