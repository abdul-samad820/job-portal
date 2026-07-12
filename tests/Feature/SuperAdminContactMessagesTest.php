<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuperAdminContactMessagesTest extends TestCase
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

    private function makeMessage(array $overrides = []): ContactMessage
    {
        return ContactMessage::create(array_merge([
            'name' => 'John Visitor',
            'email' => 'john@example.com',
            'subject' => 'Question about pricing',
            'message' => 'Do you offer a free trial?',
            'ip_address' => '192.168.1.1',
        ], $overrides));
    }

    #[Test]
    public function contact_inbox_lists_messages_with_unread_count(): void
    {
        $this->makeMessage(['read_at' => null]);
        $this->makeMessage(['read_at' => now()]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.contact.index'));

        $response->assertStatus(200);
        $this->assertEquals(1, $response->viewData('unreadCount'));
    }

    #[Test]
    public function viewing_a_message_marks_it_as_read_and_returns_json(): void
    {
        $message = $this->makeMessage(['read_at' => null]);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.contact.show', $message->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $message->id]);

        $this->assertNotNull($message->fresh()->read_at);
    }

    #[Test]
    public function viewing_an_already_read_message_does_not_change_its_read_timestamp(): void
    {
        $originalReadAt = now()->subDays(2);
        $message = $this->makeMessage(['read_at' => $originalReadAt]);

        $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.contact.show', $message->id));

        $this->assertEquals(
            $originalReadAt->toDateTimeString(),
            $message->fresh()->read_at->toDateTimeString()
        );
    }

    #[Test]
    public function superadmin_can_delete_a_contact_message(): void
    {
        $message = $this->makeMessage();

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->delete(route('superadmin.contact.destroy', $message->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }

    #[Test]
    public function regular_admin_cannot_access_the_contact_inbox(): void
    {
        $response = $this->get(route('superadmin.contact.index'));

        $response->assertRedirect(route('superadmin.login.view'));
    }
}
