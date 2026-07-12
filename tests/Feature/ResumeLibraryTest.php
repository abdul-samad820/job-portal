<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\CreatesFakeUploads;
use Tests\TestCase;

class ResumeLibraryTest extends TestCase
{
    use CreatesFakeUploads, RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    #[Test]
    public function resume_library_lists_only_the_current_users_resumes(): void
    {
        $otherUser = User::factory()->create();

        Resume::create(['user_id' => $this->user->id, 'title' => 'Mine', 'file_path' => 'resumes/mine.pdf']);
        Resume::create(['user_id' => $otherUser->id, 'title' => 'Theirs', 'file_path' => 'resumes/theirs.pdf']);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.resumes.index'));

        $response->assertStatus(200);
        $resumes = $response->viewData('resumes');
        $this->assertCount(1, $resumes);
        $this->assertEquals('Mine', $resumes->first()->title);
    }

    #[Test]
    public function user_can_upload_a_valid_pdf_resume(): void
    {
        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.resumes.store'), [
                'title' => 'Software Engineer Resume',
                'resume' => $this->fakePdf(),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('resumes', [
            'user_id' => $this->user->id,
            'title' => 'Software Engineer Resume',
        ]);
    }

    #[Test]
    public function the_first_uploaded_resume_becomes_the_default_automatically(): void
    {
        $this->actingAs($this->user, 'user')
            ->post(route('user.resumes.store'), [
                'title' => 'First Resume',
                'resume' => $this->fakePdf(),
            ]);

        $this->assertDatabaseHas('resumes', [
            'user_id' => $this->user->id,
            'title' => 'First Resume',
            'is_default' => 1,
        ]);
    }

    #[Test]
    public function a_second_uploaded_resume_is_not_default(): void
    {
        Resume::create(['user_id' => $this->user->id, 'title' => 'Existing', 'file_path' => 'resumes/a.pdf', 'is_default' => true]);

        $this->actingAs($this->user, 'user')
            ->post(route('user.resumes.store'), [
                'title' => 'Second Resume',
                'resume' => $this->fakePdf(),
            ]);

        $this->assertDatabaseHas('resumes', ['title' => 'Second Resume', 'is_default' => 0]);
    }

    #[Test]
    public function resume_upload_rejects_a_non_pdf_disguised_as_one(): void
    {
        // A file with a .pdf extension but plain-text bytes fails the
        // post-store MIME sniff — mimes:pdf catches it via magic bytes,
        // but this belt-and-braces check on genuinely malformed content
        // documents that the endpoint refuses it either way.
        $fakeFile = \Illuminate\Http\UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.resumes.store'), [
                'title' => 'Suspicious',
                'resume' => $fakeFile,
            ]);

        // UploadedFile::fake()->create() produces a .pdf-named file with
        // non-PDF content. Laravel's 'mimes:pdf' rule matches by extension
        // and lets it through — the real content check is the app's
        // belt-and-braces post-store MIME sniff, which flashes a plain
        // 'error' message rather than a validation error bag.
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('resumes', ['title' => 'Suspicious']);
    }

    #[Test]
    public function user_cannot_upload_more_than_five_resumes(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Resume::create(['user_id' => $this->user->id, 'title' => "Resume {$i}", 'file_path' => "resumes/r{$i}.pdf"]);
        }

        $response = $this->actingAs($this->user, 'user')
            ->post(route('user.resumes.store'), [
                'title' => 'Sixth Resume',
                'resume' => $this->fakePdf(),
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals(5, Resume::where('user_id', $this->user->id)->count());
    }

    #[Test]
    public function user_can_set_a_different_resume_as_default(): void
    {
        $first = Resume::create(['user_id' => $this->user->id, 'title' => 'A', 'file_path' => 'resumes/a.pdf', 'is_default' => true]);
        $second = Resume::create(['user_id' => $this->user->id, 'title' => 'B', 'file_path' => 'resumes/b.pdf', 'is_default' => false]);

        $this->actingAs($this->user, 'user')
            ->patch(route('user.resumes.default', $second->id))
            ->assertRedirect();

        $this->assertDatabaseHas('resumes', ['id' => $second->id, 'is_default' => 1]);
        $this->assertDatabaseHas('resumes', ['id' => $first->id, 'is_default' => 0]);
    }

    #[Test]
    public function user_cannot_set_another_users_resume_as_default(): void
    {
        $otherUser = User::factory()->create();
        $theirResume = Resume::create(['user_id' => $otherUser->id, 'title' => 'Theirs', 'file_path' => 'resumes/x.pdf']);

        $response = $this->actingAs($this->user, 'user')
            ->patch(route('user.resumes.default', $theirResume->id));

        $response->assertStatus(404);
    }

    #[Test]
    public function deleting_the_default_resume_promotes_the_next_most_recent_one(): void
    {
        $older = Resume::create(['user_id' => $this->user->id, 'title' => 'Older', 'file_path' => 'resumes/older.pdf', 'is_default' => false, 'created_at' => now()->subDay()]);
        $default = Resume::create(['user_id' => $this->user->id, 'title' => 'Default', 'file_path' => 'resumes/default.pdf', 'is_default' => true]);

        $this->actingAs($this->user, 'user')
            ->delete(route('user.resumes.destroy', $default->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('resumes', ['id' => $default->id]);
        $this->assertDatabaseHas('resumes', ['id' => $older->id, 'is_default' => 1]);
    }

    #[Test]
    public function deleting_a_resume_still_used_by_an_application_keeps_the_file_on_disk(): void
    {
        Storage::disk('public')->put('resumes/kept.pdf', 'dummy pdf content');
        $resume = Resume::create(['user_id' => $this->user->id, 'title' => 'Kept', 'file_path' => 'resumes/kept.pdf']);

        JobApplication::factory()->create([
            'user_id' => $this->user->id,
            'resume' => 'resumes/kept.pdf',
        ]);

        $this->actingAs($this->user, 'user')
            ->delete(route('user.resumes.destroy', $resume->id));

        Storage::disk('public')->assertExists('resumes/kept.pdf');
    }

    #[Test]
    public function user_can_download_their_own_resume(): void
    {
        Storage::disk('public')->put('resumes/download-me.pdf', 'dummy pdf content');
        $resume = Resume::create([
            'user_id' => $this->user->id, 'title' => 'Downloadable',
            'file_path' => 'resumes/download-me.pdf', 'original_name' => 'my_resume.pdf',
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.resumes.download', $resume->id));

        $response->assertStatus(200);
    }

    #[Test]
    public function downloading_a_missing_file_returns_404(): void
    {
        $resume = Resume::create([
            'user_id' => $this->user->id, 'title' => 'Ghost',
            'file_path' => 'resumes/does-not-exist.pdf',
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.resumes.download', $resume->id));

        $response->assertStatus(404);
    }

    #[Test]
    public function user_cannot_download_another_users_resume(): void
    {
        $otherUser = User::factory()->create();
        $theirResume = Resume::create(['user_id' => $otherUser->id, 'title' => 'Theirs', 'file_path' => 'resumes/theirs.pdf']);

        $response = $this->actingAs($this->user, 'user')
            ->get(route('user.resumes.download', $theirResume->id));

        $response->assertStatus(404);
    }

    #[Test]
    public function guest_cannot_access_resume_library(): void
    {
        $this->get(route('user.resumes.index'))->assertRedirect(route('user.login'));
    }
}
