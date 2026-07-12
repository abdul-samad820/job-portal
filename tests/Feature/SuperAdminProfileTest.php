<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuperAdminProfileTest extends TestCase
{
    use RefreshDatabase;

    private Admin $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = Admin::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email' => 'super@example.com',
            'password' => Hash::make('CurrentPass123'),
        ]);
    }

    #[Test]
    public function profile_page_renders(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->get(route('superadmin.profile'));

        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_can_update_email_with_correct_current_password(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->put(route('superadmin.profile.update'), [
                'email' => 'newsuper@example.com',
                'current_password' => 'CurrentPass123',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('admins', [
            'id' => $this->superAdmin->id,
            'email' => 'newsuper@example.com',
        ]);
    }

    #[Test]
    public function profile_update_fails_with_wrong_current_password(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->put(route('superadmin.profile.update'), [
                'email' => 'newsuper@example.com',
                'current_password' => 'WrongPassword',
            ]);

        $response->assertSessionHasErrors('current_password');

        $this->assertDatabaseHas('admins', [
            'id' => $this->superAdmin->id,
            'email' => 'super@example.com',
        ]);
    }

    #[Test]
    public function superadmin_can_change_password_when_confirmed_correctly(): void
    {
        $this->actingAs($this->superAdmin, 'superadmin')
            ->put(route('superadmin.profile.update'), [
                'email' => 'super@example.com',
                'current_password' => 'CurrentPass123',
                'password' => 'NewStrongPass1',
                'password_confirmation' => 'NewStrongPass1',
            ]);

        $this->assertTrue(Hash::check('NewStrongPass1', $this->superAdmin->fresh()->password));
    }

    #[Test]
    public function password_update_fails_when_confirmation_does_not_match(): void
    {
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->put(route('superadmin.profile.update'), [
                'email' => 'super@example.com',
                'current_password' => 'CurrentPass123',
                'password' => 'NewStrongPass1',
                'password_confirmation' => 'Mismatch1',
            ]);

        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('CurrentPass123', $this->superAdmin->fresh()->password));
    }

    #[Test]
    public function email_must_be_unique_among_admins(): void
    {
        Admin::factory()->create(['email' => 'taken@example.com']);

        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->put(route('superadmin.profile.update'), [
                'email' => 'taken@example.com',
                'current_password' => 'CurrentPass123',
            ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function superadmin_can_keep_their_own_email_unchanged(): void
    {
        // Rule::unique(...)->ignore($superadmin->id) should not flag the
        // superadmin's own current email as a duplicate.
        $response = $this->actingAs($this->superAdmin, 'superadmin')
            ->put(route('superadmin.profile.update'), [
                'email' => 'super@example.com',
                'current_password' => 'CurrentPass123',
            ]);

        $response->assertSessionHasNoErrors();
    }
}
