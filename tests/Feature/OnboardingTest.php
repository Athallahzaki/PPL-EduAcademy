<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_onboarding_saves_education_level_and_sets_onboarded_at(): void
    {
        $student = User::factory()->student()->active()->unonboarded()->create();

        $response = $this->actingAs($student)->post('/onboarding', [
            'education_level' => 'university',
        ]);

        $response->assertRedirect('/student/dashboard');

        $student->refresh();
        $this->assertEquals('university', $student->education_level);
        $this->assertNotNull($student->onboarded_at);
        $this->assertFalse($student->needsOnboarding());
    }

    public function test_instructor_onboarding_validates_and_stores_cv_and_creates_profile(): void
    {
        Storage::fake('local');

        $instructor = User::factory()->instructor()->active()->unonboarded()->create();

        $pdf = UploadedFile::fake()->create('resume.pdf', 500, 'application/pdf');

        $response = $this->actingAs($instructor)->post('/onboarding', [
            'bio' => 'Passionate computer science educator with a background in software development.',
            'expertise' => 'Web Development',
            'portfolio_url' => 'https://example.com/portfolio',
            'cv' => $pdf,
        ]);

        $response->assertRedirect('/instructor/dashboard');

        $instructor->refresh();
        $this->assertNotNull($instructor->onboarded_at);
        $this->assertNotNull($instructor->instructorProfile);
        $this->assertEquals('Web Development', $instructor->instructorProfile->expertise);

        // Verify CV stored on local/private disk
        Storage::disk('local')->assertExists($instructor->instructorProfile->cv_path);
    }

    public function test_changing_password_requires_current_password_and_clears_flag(): void
    {
        $user = User::factory()->student()->active()->mustChangePassword()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        // Wrong current password
        $failResponse = $this->actingAs($user)->put('/password/change', [
            'current_password' => 'wrong-pass',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);
        $failResponse->assertSessionHasErrors('current_password');

        // Same password as current fails different rule
        $sameResponse = $this->actingAs($user)->put('/password/change', [
            'current_password' => 'old-password-123',
            'password' => 'old-password-123',
            'password_confirmation' => 'old-password-123',
        ]);
        $sameResponse->assertSessionHasErrors('password');

        // Valid change
        $successResponse = $this->actingAs($user)->put('/password/change', [
            'current_password' => 'old-password-123',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $successResponse->assertRedirect('/student/dashboard');

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check('new-password-123', $user->password));
    }
}

