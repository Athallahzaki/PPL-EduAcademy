<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_registration_succeeds_and_redirects_to_onboarding(): void
    {
        $response = $this->post('/register', [
            'name' => 'Alice Student',
            'email' => 'ALICE@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'register_as' => 'student',
        ]);

        $response->assertRedirect('/onboarding');
        $this->assertAuthenticated();

        $user = User::where('email', 'alice@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(Role::Student, $user->role);
        $this->assertEquals(AccountStatus::Active, $user->status);
        $this->assertNull($user->onboarded_at);
        $this->assertFalse($user->must_change_password);
    }

    public function test_instructor_registration_creates_pending_account_and_does_not_log_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'Bob Instructor',
            'email' => 'bob@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'register_as' => 'instructor',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('status', 'Registration received. An administrator must approve your account before you can sign in.');
        $this->assertGuest();

        $user = User::where('email', 'bob@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(Role::Instructor, $user->role);
        $this->assertEquals(AccountStatus::Pending, $user->status);
        $this->assertNull($user->onboarded_at);
    }

    public function test_tampering_with_registration_cannot_create_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'Hacker Admin',
            'email' => 'hacker@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'register_as' => 'admin', // invalid register_as
            'role' => 'admin',
        ]);

        $response->assertSessionHasErrors('register_as');
        $this->assertDatabaseMissing('users', ['email' => 'hacker@example.com']);
    }

    public function test_duplicate_email_fails_validation_including_soft_deleted_and_rejected(): void
    {
        // Existing active user
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name' => 'Duplicate Test',
            'email' => 'existing@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'register_as' => 'student',
        ]);
        $response->assertSessionHasErrors('email');

        // Soft deleted user
        $softDeleted = User::factory()->create(['email' => 'deleted@example.com']);
        $softDeleted->delete();

        $response2 = $this->post('/register', [
            'name' => 'Deleted Duplicate',
            'email' => 'deleted@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'register_as' => 'student',
        ]);
        $response2->assertSessionHasErrors('email');
    }

    public function test_invalid_email_short_password_or_mismatched_confirmation_fails(): void
    {
        $response = $this->post('/register', [
            'name' => 'Invalid Test',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
            'register_as' => 'student',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_with_wrong_credentials_returns_generic_error(): void
    {
        User::factory()->create([
            'email' => 'active@example.com',
            'password' => Hash::make('password123'),
            'status' => AccountStatus::Active,
        ]);

        $response = $this->post('/login', [
            'email' => 'active@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(
            __('These credentials do not match our records.'),
            session('errors')->get('email')[0]
        );
        $this->assertGuest();
    }

    public function test_login_with_correct_credentials_but_pending_rejected_or_suspended_is_refused(): void
    {
        $password = 'password123';

        // 1. Pending
        $pending = User::factory()->instructor()->pending()->create(['password' => Hash::make($password)]);
        $resPending = $this->post('/login', ['email' => $pending->email, 'password' => $password]);
        $resPending->assertSessionHasErrors('email');
        $this->assertStringContainsString('awaiting administrator approval', session('errors')->get('email')[0]);
        $this->assertGuest();

        // 2. Rejected
        $rejected = User::factory()->instructor()->rejected()->create(['password' => Hash::make($password)]);
        $resRejected = $this->post('/login', ['email' => $rejected->email, 'password' => $password]);
        $resRejected->assertSessionHasErrors('email');
        $this->assertStringContainsString('not approved', session('errors')->get('email')[0]);
        $this->assertGuest();

        // 3. Suspended
        $suspended = User::factory()->student()->suspended()->create(['password' => Hash::make($password)]);
        $resSuspended = $this->post('/login', ['email' => $suspended->email, 'password' => $password]);
        $resSuspended->assertSessionHasErrors('email');
        $this->assertStringContainsString('suspended', session('errors')->get('email')[0]);
        $this->assertGuest();
    }

    public function test_roles_redirect_to_own_dashboards_after_login(): void
    {
        $password = 'password123';

        // Admin
        $admin = User::factory()->admin()->create(['password' => Hash::make($password)]);
        $this->post('/login', ['email' => $admin->email, 'password' => $password])
            ->assertRedirect('/admin/dashboard');
        $this->post('/logout');

        // Instructor
        $instructor = User::factory()->instructor()->active()->create(['password' => Hash::make($password)]);
        $this->post('/login', ['email' => $instructor->email, 'password' => $password])
            ->assertRedirect('/instructor/dashboard');
        $this->post('/logout');

        // Student
        $student = User::factory()->student()->active()->create(['password' => Hash::make($password)]);
        $this->post('/login', ['email' => $student->email, 'password' => $password])
            ->assertRedirect('/student/dashboard');
        $this->post('/logout');
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'throttle@example.com',
                'password' => 'wrong',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'throttle@example.com',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Too many attempts', session('errors')->get('email')[0]);
    }

    public function test_authenticated_user_visiting_login_redirects_to_dashboard(): void
    {
        $student = User::factory()->student()->active()->create();

        $response = $this->actingAs($student)->get('/login');
        $response->assertRedirect('/student/dashboard');
    }

    public function test_logout_invalidates_session_and_redirects_to_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}

