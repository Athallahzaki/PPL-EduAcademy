<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * PBI-04: Instructor profile & specialization.
 */
class InstructorProfileTest extends TestCase
{
    use RefreshDatabase;

    private function instructor(): User
    {
        return User::factory()->instructor()->active()->create([
            'onboarded_at' => now(),
            'must_change_password' => false,
        ]);
    }

    public function test_instructor_can_view_and_open_edit_page(): void
    {
        $user = $this->instructor();

        $this->actingAs($user)->get(route('instructor.profile.show'))->assertOk();
        $this->actingAs($user)->get(route('instructor.profile.edit'))->assertOk();
    }

    public function test_instructor_can_update_profile_and_upload_cv(): void
    {
        Storage::fake('local');
        $user = $this->instructor();

        $this->actingAs($user)->put(route('instructor.profile.update'), [
            'bio' => 'Backend engineer and teacher.',
            'expertise' => 'PHP, Laravel',
            'portfolio_url' => 'https://example.com/portfolio',
            'cv' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('instructor.profile.show'));

        $profile = $user->fresh()->instructorProfile;
        $this->assertSame('PHP, Laravel', $profile->expertise);
        $this->assertSame('https://example.com/portfolio', $profile->portfolio_url);
        Storage::disk('local')->assertExists($profile->cv_path);

        $this->actingAs($user->fresh())
            ->get(route('instructor.profile.show'))
            ->assertOk()
            ->assertSee('Backend engineer and teacher.')
            ->assertSee('PHP')
            ->assertSee('Laravel');
    }

    public function test_replacing_cv_deletes_the_previous_file(): void
    {
        Storage::fake('local');
        $user = $this->instructor();
        $payload = fn () => [
            'bio' => 'Bio',
            'expertise' => 'Math',
            'cv' => UploadedFile::fake()->create('cv.pdf', 50, 'application/pdf'),
        ];

        $this->actingAs($user)->put(route('instructor.profile.update'), $payload());
        $oldPath = $user->fresh()->instructorProfile->cv_path;

        $this->actingAs($user->fresh())->put(route('instructor.profile.update'), $payload());
        $newPath = $user->fresh()->instructorProfile->cv_path;

        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertExists($newPath);
    }

    public function test_profile_without_new_cv_keeps_existing_cv(): void
    {
        Storage::fake('local');
        $user = $this->instructor();

        $this->actingAs($user)->put(route('instructor.profile.update'), [
            'bio' => 'Bio',
            'expertise' => 'Math',
            'cv' => UploadedFile::fake()->create('cv.pdf', 50, 'application/pdf'),
        ]);
        $path = $user->fresh()->instructorProfile->cv_path;

        $this->actingAs($user->fresh())->put(route('instructor.profile.update'), [
            'bio' => 'Updated bio',
            'expertise' => 'Math',
        ])->assertRedirect(route('instructor.profile.show'));

        $profile = $user->fresh()->instructorProfile;
        $this->assertSame('Updated bio', $profile->bio);
        $this->assertSame($path, $profile->cv_path);
    }

    public function test_validation_rejects_bad_input(): void
    {
        Storage::fake('local');
        $user = $this->instructor();

        $this->actingAs($user)->put(route('instructor.profile.update'), [
            'bio' => '',
            'expertise' => '',
        ])->assertSessionHasErrors(['bio', 'expertise']);

        $this->actingAs($user)->put(route('instructor.profile.update'), [
            'bio' => 'Bio',
            'expertise' => 'Math',
            'portfolio_url' => 'not-a-url',
            'cv' => UploadedFile::fake()->create('cv.docx', 50),
        ])->assertSessionHasErrors(['portfolio_url', 'cv']);

        $this->actingAs($user)->put(route('instructor.profile.update'), [
            'bio' => 'Bio',
            'expertise' => 'Math',
            'cv' => UploadedFile::fake()->create('big.pdf', 3000, 'application/pdf'),
        ])->assertSessionHasErrors('cv');
    }

    public function test_cv_download_works_and_404s_without_cv(): void
    {
        Storage::fake('local');
        $user = $this->instructor();

        $this->actingAs($user)->get(route('instructor.profile.cv'))->assertNotFound();

        $this->actingAs($user)->put(route('instructor.profile.update'), [
            'bio' => 'Bio',
            'expertise' => 'Math',
            'cv' => UploadedFile::fake()->create('cv.pdf', 50, 'application/pdf'),
        ]);

        $this->actingAs($user->fresh())->get(route('instructor.profile.cv'))->assertOk();
    }

    public function test_students_and_guests_cannot_access_instructor_profile(): void
    {
        $student = User::factory()->student()->active()->create([
            'onboarded_at' => now(),
            'must_change_password' => false,
        ]);

        $this->actingAs($student)->get(route('instructor.profile.show'))->assertForbidden();
        $this->actingAs($student)->put(route('instructor.profile.update'), [
            'bio' => 'x',
            'expertise' => 'y',
        ])->assertForbidden();

        auth()->logout();
        $this->get(route('instructor.profile.show'))->assertRedirect();
    }

    public function test_dashboard_links_to_profile(): void
    {
        $this->actingAs($this->instructor())
            ->get(route('instructor.dashboard'))
            ->assertOk()
            ->assertSee(route('instructor.profile.show'));
    }
}
