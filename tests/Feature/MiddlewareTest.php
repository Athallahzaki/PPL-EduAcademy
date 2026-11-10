<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_protected_routes(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/instructor/dashboard')->assertRedirect('/login');
        $this->get('/student/dashboard')->assertRedirect('/login');
    }

    public function test_wrong_role_receives_403_forbidden(): void
    {
        $student = User::factory()->student()->active()->create();
        $instructor = User::factory()->instructor()->active()->create();

        // Student accessing admin or instructor areas
        $this->actingAs($student)->get('/admin/dashboard')->assertStatus(403);
        $this->actingAs($student)->get('/instructor/dashboard')->assertStatus(403);

        // Instructor accessing admin or student areas
        $this->actingAs($instructor)->get('/admin/dashboard')->assertStatus(403);
        $this->actingAs($instructor)->get('/student/dashboard')->assertStatus(403);
    }

    public function test_user_suspended_while_logged_in_is_logged_out_on_next_request(): void
    {
        $user = User::factory()->student()->active()->create();

        $this->actingAs($user);
        $this->get('/student/dashboard')->assertStatus(200);

        // User gets suspended
        $user->status = AccountStatus::Suspended;
        $user->save();

        $response = $this->get('/student/dashboard');
        $response->assertRedirect('/login');
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_soft_deleted_user_session_ends_on_next_request(): void
    {
        $user = User::factory()->student()->active()->create();

        $this->actingAs($user);
        $this->get('/student/dashboard')->assertStatus(200);

        // Soft delete user
        $user->delete();

        $response = $this->get('/student/dashboard');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_forced_password_change_redirects_until_changed(): void
    {
        $user = User::factory()->student()->active()->mustChangePassword()->create();

        $response = $this->actingAs($user)->get('/student/dashboard');
        $response->assertRedirect('/password/change');

        // Can access the password change page itself
        $this->actingAs($user)->get('/password/change')->assertStatus(200);
    }

    public function test_unonboarded_user_redirects_to_onboarding_page(): void
    {
        $student = User::factory()->student()->active()->unonboarded()->create();

        $response = $this->actingAs($student)->get('/student/dashboard');
        $response->assertRedirect('/onboarding');

        // Can access onboarding page
        $this->actingAs($student)->get('/onboarding')->assertStatus(200);
    }

    public function test_admin_is_never_sent_to_onboarding(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/dashboard')->assertStatus(200);
        $this->assertFalse($admin->needsOnboarding());
    }
}

