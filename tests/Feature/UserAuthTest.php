<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;


class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake(); 
    }

 #[Test]
public function user_can_register_with_valid_data(): void
{
    
    \Illuminate\Support\Facades\Mail::fake();
    \Illuminate\Support\Facades\Notification::fake();

    $response = $this->post(route('user.register'), [
        'name'                  => 'Samad Khan',
        'email'                 => 'samad@example.com',
        'password'              => 'Password@123',
        'password_confirmation' => 'Password@123',
    ]);

    $this->assertDatabaseHas('users', [
       'email' => 'samad@example.com',   
    ]);

    $response->assertRedirect();
}

   #[Test]
    public function user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'samad@example.com']);

        $response = $this->post(route('user.register'), [
            'name' => 'Another User',
            'email' => 'samad@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertSessionHasErrors('email');
    }

  #[Test]
    public function user_cannot_register_with_weak_password(): void
    {
        $response = $this->post(route('user.register'), [
            'name' => 'Samad Khan',
            'email' => 'samad@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors('password');
    }

  #[Test]
    public function user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'samad@example.com',
            'password' => bcrypt('Password@123'),
        ]);

        $response = $this->post(route('user.login'), [
            'email' => 'samad@example.com',
            'password' => 'Password@123',
        ]);

        $this->assertAuthenticatedAs($user, 'user');
        $response->assertRedirect(route('user.dashboard'));
    }

  #[Test]
    public function user_cannot_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'samad@example.com',
            'password' => bcrypt('correct_password'),
        ]);

        $response = $this->post(route('user.login'), [
            'email' => 'samad@example.com',
            'password' => 'wrong_password',
        ]);

        $this->assertGuest('user');
        $response->assertSessionHasErrors('email');
    }

  #[Test]
    public function user_cannot_access_dashboard_without_login(): void
    {
        $response = $this->get(route('user.dashboard'));
        $response->assertRedirect(route('user.login'));
    }

  #[Test]
    public function user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'user')
            ->post(route('user.logout'));

        $this->assertGuest('user');
    }

    /** @test */
    public function unverified_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user, 'user')
            ->get(route('user.dashboard'));

        // Should be redirected to verification notice
        $response->assertRedirect(route('verification.notice'));
    }
}
