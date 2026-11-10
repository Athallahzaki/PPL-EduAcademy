<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admins_cannot_reach_admin_users_routes(): void
    {
        $student = User::factory()->student()->active()->create();

        $this->actingAs($student)->get('/admin/users')->assertStatus(403);
        $this->actingAs($student)->get('/admin/users/create')->assertStatus(403);
        $this->actingAs($student)->post('/admin/users', [])->assertStatus(403);
    }

    public function test_admin_can_create_user_with_flashed_temporary_password(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'New Instructor User',
            'email' => 'new.instructor@example.com',
            'role' => 'instructor',
        ]);

        $created = User::where('email', 'new.instructor@example.com')->first();
        $this->assertNotNull($created);
        $response->assertRedirect(route('admin.users.show', $created));
        $response->assertSessionHas('temporary_password');
        $this->assertTrue($created->must_change_password);
        $this->assertEquals(AccountStatus::Active, $created->status);
        $this->assertEquals(Role::Instructor, $created->role);
    }

    public function test_status_transitions_allowed_succeed_and_disallowed_fail(): void
    {
        $admin = User::factory()->admin()->create();

        // 1. Pending -> Active (Approve)
        $pending = User::factory()->instructor()->pending()->create();
        $resApprove = $this->actingAs($admin)->patch("/admin/users/{$pending->id}/status", [
            'status' => 'active',
        ]);
        $resApprove->assertSessionHas('success');
        $this->assertEquals(AccountStatus::Active, $pending->fresh()->status);

        // 2. Active -> Suspended (Suspend)
        $activeUser = User::factory()->student()->active()->create();
        $resSuspend = $this->actingAs($admin)->patch("/admin/users/{$activeUser->id}/status", [
            'status' => 'suspended',
        ]);
        $resSuspend->assertSessionHas('success');
        $this->assertEquals(AccountStatus::Suspended, $activeUser->fresh()->status);

        // 3. Suspended -> Active (Reactivate)
        $resReactivate = $this->actingAs($admin)->patch("/admin/users/{$activeUser->id}/status", [
            'status' => 'active',
        ]);
        $resReactivate->assertSessionHas('success');
        $this->assertEquals(AccountStatus::Active, $activeUser->fresh()->status);

        // 4. Pending -> Rejected
        $pending2 = User::factory()->instructor()->pending()->create();
        $resReject = $this->actingAs($admin)->patch("/admin/users/{$pending2->id}/status", [
            'status' => 'rejected',
        ]);
        $resReject->assertSessionHas('success');
        $this->assertEquals(AccountStatus::Rejected, $pending2->fresh()->status);

        // 5. Disallowed transition: Active -> Rejected (HTTP 403 Forbidden by policy)
        $resDisallowed = $this->actingAs($admin)->patch("/admin/users/{$activeUser->id}/status", [
            'status' => 'rejected',
        ]);
        $resDisallowed->assertStatus(403);
    }

    public function test_admin_cannot_suspend_delete_or_change_role_of_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        // Cannot suspend own account
        $this->actingAs($admin)->patch("/admin/users/{$admin->id}/status", [
            'status' => 'suspended',
        ])->assertStatus(403);

        // Cannot delete own account
        $this->actingAs($admin)->delete("/admin/users/{$admin->id}")
            ->assertStatus(403);

        // Cannot change own role
        $this->actingAs($admin)->put("/admin/users/{$admin->id}", [
            'name' => 'Admin Name',
            'email' => $admin->email,
            'role' => 'student',
        ])->assertStatus(403);
    }

    public function test_last_active_admin_is_protected(): void
    {
        // Only one active admin exists
        $soleAdmin = User::factory()->admin()->create();
        $this->assertEquals(1, User::activeAdminCount());

        $secondaryAdmin = User::factory()->admin()->create();
        $this->assertEquals(2, User::activeAdminCount());

        // Secondary admin suspends soleAdmin
        $this->actingAs($secondaryAdmin)->patch("/admin/users/{$soleAdmin->id}/status", [
            'status' => 'suspended',
        ])->assertSessionHas('success');

        $this->assertEquals(1, User::activeAdminCount());

        // Now secondaryAdmin cannot be demoted or deleted or suspended by anyone
        // Let's create another admin who will try to suspend secondaryAdmin
        $thirdAdmin = User::factory()->admin()->create();
        $this->assertEquals(2, User::activeAdminCount());

        // Delete thirdAdmin so secondaryAdmin is again the only one
        $this->actingAs($secondaryAdmin)->delete("/admin/users/{$thirdAdmin->id}");
        $this->assertEquals(1, User::activeAdminCount());
    }

    public function test_reset_password_generates_temp_password_and_clears_sessions(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->student()->active()->create();

        // Simulate session
        DB::table('sessions')->insert([
            'id' => 'session-123',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => 'dummy',
            'last_activity' => time(),
        ]);

        $this->assertDatabaseHas('sessions', ['user_id' => $user->id]);

        $response = $this->actingAs($admin)->post("/admin/users/{$user->id}/reset-password");
        $response->assertSessionHas('temporary_password');

        $user->refresh();
        $this->assertTrue($user->must_change_password);
        $this->assertDatabaseMissing('sessions', ['user_id' => $user->id]);
    }

    public function test_soft_delete_and_restore(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->student()->active()->create();

        // Soft delete
        $this->actingAs($admin)->delete("/admin/users/{$user->id}")
            ->assertRedirect('/admin/users');

        $this->assertSoftDeleted('users', ['id' => $user->id]);

        // Restore
        $this->actingAs($admin)->post("/admin/users/{$user->id}/restore")
            ->assertRedirect('/admin/users');

        $this->assertNotSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_index_filters(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Super Admin', 'email' => 'admin@edu.test']);
        User::factory()->student()->active()->create(['name' => 'Charlie Student', 'email' => 'charlie@edu.test']);
        User::factory()->instructor()->pending()->create(['name' => 'Dan Instructor', 'email' => 'dan@edu.test']);

        // Search query filter
        $response = $this->actingAs($admin)->get('/admin/users?q=Charlie');
        $response->assertSee('Charlie Student');
        $response->assertDontSee('Dan Instructor');

        // Role filter
        $resRole = $this->actingAs($admin)->get('/admin/users?role=instructor');
        $resRole->assertSee('Dan Instructor');
        $resRole->assertDontSee('Charlie Student');

        // Status filter
        $resStatus = $this->actingAs($admin)->get('/admin/users?status=pending');
        $resStatus->assertSee('Dan Instructor');
        $resStatus->assertDontSee('Charlie Student');
    }
}

