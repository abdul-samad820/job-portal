<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_via_api(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Samad Khan',
            'email' => 'samad@test.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status', 'message',
                'data' => ['user', 'token', 'token_type'],
            ])
            ->assertJson(['status' => 'success']);
    }

    /** @test */
    public function user_can_login_via_api(): void
    {
        User::factory()->create([
            'email' => 'samad@test.com',
            'password' => bcrypt('Password@123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'samad@test.com',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data' => ['token'],
            ]);
    }

    /** @test */
    public function unauthenticated_user_gets_401()
    {
        $response = $this->getJson('/api/v1/applications');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /** @test */
    public function user_can_logout_via_api(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        // Token delete hua?
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
