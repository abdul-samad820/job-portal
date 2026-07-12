<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserProfileAndAccountTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'password' => Hash::make('CurrentPass123'),
        ]);
    }

    #[Test]
    public function profile_page_renders_for_a_user_with_no_profile_yet(): void
    {
        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.profile'));

        $response->assertStatus(200);
    }

    #[Test]
    public function add_profile_form_creates_an_empty_profile_row_if_none_exists(): void
    {
        $this->assertDatabaseMissing('user_profiles', ['user_id' => $this->user->id]);

        $this->actingAs($this->user, 'user')
            ->get(route('user.add_profile'))
            ->assertStatus(200);

        $this->assertDatabaseHas('user_profiles', ['user_id' => $this->user->id]);
    }

    #[Test]
    public function user_can_update_their_profile_summary_and_skills(): void
    {
        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.update_profile'), [
                'professional_summary' => 'Backend developer with 3 years experience.',
                'core_skills' => 'PHP, Laravel, MySQL',
            ]);

        $response->assertRedirect(route('user.profile'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $this->user->id,
            'professional_summary' => 'Backend developer with 3 years experience.',
            'core_skills' => 'PHP, Laravel, MySQL',
        ]);
    }

    #[Test]
    public function profile_update_stores_education_experience_and_projects_as_arrays(): void
    {
        $this->actingAs($this->user, 'user')
            ->post(route('user.update_profile'), [
                'education' => [
                    ['degree' => 'B.Tech', 'institute' => 'ABC University', 'year' => '2022'],
                ],
                'experience' => [
                    ['company' => 'Acme', 'role' => 'Developer', 'duration' => '2022-2024', 'description' => 'Built stuff'],
                ],
                'projects' => [
                    ['title' => 'Portfolio Site', 'tech' => 'Laravel', 'link' => 'https://example.com', 'description' => 'My site'],
                ],
            ]);

        $profile = UserProfile::where('user_id', $this->user->id)->first();

        $this->assertEquals('B.Tech', $profile->education[0]['degree']);
        $this->assertEquals('Acme', $profile->experience[0]['company']);
        $this->assertEquals('Portfolio Site', $profile->projects[0]['title']);
    }

    #[Test]
    public function user_can_upload_a_valid_profile_image(): void
    {
        $image = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.update_profile'), [
                'profile_image' => $image,
            ]);

        $response->assertRedirect(route('user.profile'));

        $profile = UserProfile::where('user_id', $this->user->id)->first();
        $this->assertNotNull($profile->profile_image);
        Storage::disk('public')->assertExists('user_profile/'.$profile->profile_image);
    }

    #[Test]
    public function profile_update_rejects_a_non_image_file_for_profile_image(): void
    {
        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.update_profile'), [
                'profile_image' => $file,
            ]);

        $response->assertSessionHasErrors('profile_image');
    }

    #[Test]
    public function uploading_a_new_profile_image_deletes_the_old_one(): void
    {
        $firstImage = UploadedFile::fake()->image('first.jpg');
        $this->actingAs($this->user, 'user')->post(route('user.update_profile'), ['profile_image' => $firstImage]);

        $oldFilename = UserProfile::where('user_id', $this->user->id)->first()->profile_image;
        Storage::disk('public')->assertExists('user_profile/'.$oldFilename);

        $secondImage = UploadedFile::fake()->image('second.jpg');
        $this->actingAs($this->user, 'user')->post(route('user.update_profile'), ['profile_image' => $secondImage]);

        Storage::disk('public')->assertMissing('user_profile/'.$oldFilename);
    }

    #[Test]
    public function account_settings_page_renders(): void
    {
        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.account_setting'));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_update_name_email_and_phone(): void
    {
        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.account_setting_update'), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'phone' => '9876543210',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '9876543210',
        ]);
    }

    #[Test]
    public function updating_email_to_one_already_taken_fails_validation(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.account_setting_update'), [
                'name' => $this->user->name,
                'email' => 'taken@example.com',
                'phone' => '9876543210',
            ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function user_can_change_password_with_correct_current_password(): void
    {
        $this->actingAs($this->user, 'user')
            ->post(route('user.account_setting_update'), [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => '9876543210',
                'current_password' => 'CurrentPass123',
                'password' => 'NewStrongPass1',
                'password_confirmation' => 'NewStrongPass1',
            ]);

        $this->assertTrue(Hash::check('NewStrongPass1', $this->user->fresh()->password));
    }

    #[Test]
    public function password_change_fails_with_wrong_current_password(): void
    {
        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.account_setting_update'), [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => '9876543210',
                'current_password' => 'WrongPassword',
                'password' => 'NewStrongPass1',
                'password_confirmation' => 'NewStrongPass1',
            ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('CurrentPass123', $this->user->fresh()->password));
    }

    #[Test]
    public function changing_password_logs_a_password_changed_activity_entry(): void
    {
        $this->actingAs($this->user, 'user')
            ->post(route('user.account_setting_update'), [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => '9876543210',
                'current_password' => 'CurrentPass123',
                'password' => 'NewStrongPass1',
                'password_confirmation' => 'NewStrongPass1',
            ]);

        $this->assertDatabaseHas('user_activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'password_changed',
        ]);
    }

    #[Test]
    public function user_can_toggle_open_to_work_status(): void
    {
        $this->actingAs($this->user, 'user')
            ->post(route('user.open_to_work.toggle'))
            ->assertRedirect();

        $this->assertDatabaseHas('user_profiles', ['user_id' => $this->user->id, 'open_to_work' => 1]);

        $this->actingAs($this->user, 'user')
            ->post(route('user.open_to_work.toggle'))
            ->assertRedirect();

        $this->assertDatabaseHas('user_profiles', ['user_id' => $this->user->id, 'open_to_work' => 0]);
    }

    #[Test]
    public function guest_cannot_update_profile_or_account_settings(): void
    {
        $this->post(route('user.update_profile'), [])->assertRedirect(route('user.login'));
        $this->post(route('user.account_setting_update'), [])->assertRedirect(route('user.login'));
    }
}
