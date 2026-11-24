<?php

namespace Tests\Feature;

use App\Enums\ScholarshipStatus;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminScholarshipManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_admin_scholarships(): void
    {
        $this->get(route('admin.scholarships.index'))->assertRedirect(route('login'));
        $this->get(route('admin.scholarships.create'))->assertRedirect(route('login'));
        $this->post(route('admin.scholarships.store'), [])->assertRedirect(route('login'));
    }

    public function test_students_and_instructors_cannot_access_admin_scholarships(): void
    {
        $student = User::factory()->student()->active()->create();
        $instructor = User::factory()->instructor()->active()->create();

        $admin = User::factory()->admin()->create();
        $scholarship = Scholarship::factory()->create(['admin_id' => $admin->id]);

        // Student forbidden
        $this->actingAs($student)->get(route('admin.scholarships.index'))->assertForbidden();
        $this->actingAs($student)->get(route('admin.scholarships.create'))->assertForbidden();
        $this->actingAs($student)->post(route('admin.scholarships.store'), [])->assertForbidden();
        $this->actingAs($student)->get(route('admin.scholarships.show', $scholarship))->assertForbidden();
        $this->actingAs($student)->get(route('admin.scholarships.edit', $scholarship))->assertForbidden();
        $this->actingAs($student)->put(route('admin.scholarships.update', $scholarship), [])->assertForbidden();
        $this->actingAs($student)->delete(route('admin.scholarships.destroy', $scholarship))->assertForbidden();

        // Instructor forbidden
        $this->actingAs($instructor)->get(route('admin.scholarships.index'))->assertForbidden();
        $this->actingAs($instructor)->get(route('admin.scholarships.create'))->assertForbidden();
        $this->actingAs($instructor)->post(route('admin.scholarships.store'), [])->assertForbidden();
        $this->actingAs($instructor)->get(route('admin.scholarships.show', $scholarship))->assertForbidden();
        $this->actingAs($instructor)->get(route('admin.scholarships.edit', $scholarship))->assertForbidden();
        $this->actingAs($instructor)->put(route('admin.scholarships.update', $scholarship), [])->assertForbidden();
        $this->actingAs($instructor)->delete(route('admin.scholarships.destroy', $scholarship))->assertForbidden();
    }

    public function test_admin_can_view_scholarship_listing(): void
    {
        $admin = User::factory()->admin()->create();

        $scholarship = Scholarship::factory()->create([
            'admin_id' => $admin->id,
            'title' => 'Beasiswa Prestasi Nasional 2026',
            'quota' => 50,
            'status' => ScholarshipStatus::Published,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.scholarships.index'));

        $response->assertOk()
            ->assertSee('Scholarship Listings')
            ->assertSee('Beasiswa Prestasi Nasional 2026')
            ->assertSee('50 seats')
            ->assertSee('Published');
    }

    public function test_admin_can_create_and_publish_new_scholarship(): void
    {
        $admin = User::factory()->admin()->create();

        $payload = [
            'title' => 'Beasiswa Talenta Digital AI 2026',
            'description' => 'Program pelatihan intensif AI dan Machine Learning.',
            'requirements' => "1. Siswa/Mahasiswa aktif di Indonesia.\n2. Melampirkan CV dan transkrip nilai.",
            'quota' => 100,
            'start_date' => now()->toDateString(),
            'deadline' => now()->addDays(40)->toDateString(),
            'status' => 'published',
        ];

        $response = $this->actingAs($admin)
            ->post(route('admin.scholarships.store'), $payload);

        $scholarship = Scholarship::query()->where('title', 'Beasiswa Talenta Digital AI 2026')->firstOrFail();

        $response->assertRedirect(route('admin.scholarships.show', $scholarship))
            ->assertSessionHas('success');

        $this->assertSame($admin->id, $scholarship->admin_id);
        $this->assertSame(100, $scholarship->quota);
        $this->assertSame(ScholarshipStatus::Published, $scholarship->status);
        $this->assertTrue($scholarship->isOpen());
    }

    public function test_invalid_scholarship_data_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.scholarships.create'))
            ->post(route('admin.scholarships.store'), [
                'title' => '',
                'requirements' => '',
                'quota' => 0,
                'start_date' => now()->addDays(10)->toDateString(),
                'deadline' => now()->addDays(5)->toDateString(), // deadline before start_date
                'status' => 'invalid-status',
            ]);

        $response->assertRedirect(route('admin.scholarships.create'))
            ->assertSessionHasErrors(['title', 'requirements', 'quota', 'deadline', 'status']);
    }

    public function test_admin_can_view_scholarship_detail(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $scholarship = Scholarship::factory()->create([
            'admin_id' => $admin->id,
            'title' => 'Beasiswa Inovasi Riset 2026',
            'requirements' => 'Harus melampirkan proposal riset minimal 10 halaman.',
            'quota' => 25,
            'status' => ScholarshipStatus::Published,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.scholarships.show', $scholarship));

        $response->assertOk()
            ->assertSee('Beasiswa Inovasi Riset 2026')
            ->assertSee('Harus melampirkan proposal riset')
            ->assertSee('25')
            ->assertSee('Admin Utama');
    }

    public function test_admin_can_view_edit_scholarship_page(): void
    {
        $admin = User::factory()->admin()->create();
        $scholarship = Scholarship::factory()->create([
            'admin_id' => $admin->id,
            'title' => 'Beasiswa yang Diedit',
            'quota' => 45,
            'status' => ScholarshipStatus::Draft,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.scholarships.edit', $scholarship));

        $response->assertOk()
            ->assertSee('Edit Scholarship')
            ->assertSee('Beasiswa yang Diedit')
            ->assertSee('45');
    }

    public function test_admin_can_update_scholarship(): void
    {
        $admin = User::factory()->admin()->create();
        $scholarship = Scholarship::factory()->create([
            'admin_id' => $admin->id,
            'title' => 'Judul Lama',
            'quota' => 30,
            'status' => ScholarshipStatus::Draft,
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.scholarships.update', $scholarship), [
                'title' => 'Judul Baru yang Diperbarui',
                'description' => 'Deskripsi baru.',
                'requirements' => 'Syarat baru yang sudah direvisi.',
                'quota' => 75,
                'start_date' => now()->toDateString(),
                'deadline' => now()->addDays(20)->toDateString(),
                'status' => 'published',
            ]);

        $response->assertRedirect(route('admin.scholarships.show', $scholarship))
            ->assertSessionHas('success');

        $fresh = $scholarship->fresh();
        $this->assertSame('Judul Baru yang Diperbarui', $fresh->title);
        $this->assertSame(75, $fresh->quota);
        $this->assertSame(ScholarshipStatus::Published, $fresh->status);
    }

    public function test_admin_can_soft_delete_and_restore_scholarship(): void
    {
        $admin = User::factory()->admin()->create();
        $scholarship = Scholarship::factory()->create([
            'admin_id' => $admin->id,
            'title' => 'Beasiswa untuk Dihapus',
        ]);

        // Soft delete
        $deleteResponse = $this->actingAs($admin)
            ->delete(route('admin.scholarships.destroy', $scholarship));

        $deleteResponse->assertRedirect(route('admin.scholarships.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('scholarships', ['id' => $scholarship->id]);

        // Restore
        $restoreResponse = $this->actingAs($admin)
            ->post(route('admin.scholarships.restore', $scholarship->id));

        $restoreResponse->assertRedirect(route('admin.scholarships.index'))
            ->assertSessionHas('success');

        $this->assertNotSoftDeleted('scholarships', ['id' => $scholarship->id]);
    }

    public function test_admin_can_search_and_filter_scholarships(): void
    {
        $admin = User::factory()->admin()->create();

        Scholarship::factory()->create([
            'admin_id' => $admin->id,
            'title' => 'Beasiswa Robotika AI',
            'status' => ScholarshipStatus::Published,
        ]);
        Scholarship::factory()->create([
            'admin_id' => $admin->id,
            'title' => 'Beasiswa Seni Musik',
            'status' => ScholarshipStatus::Draft,
        ]);
        Scholarship::factory()->create([
            'admin_id' => $admin->id,
            'title' => 'Beasiswa Bahasa Asing',
            'status' => ScholarshipStatus::Closed,
        ]);

        // Filter by keyword
        $searchResponse = $this->actingAs($admin)
            ->get(route('admin.scholarships.index', ['q' => 'Robotika']));
        $searchResponse->assertOk()
            ->assertSee('Beasiswa Robotika AI')
            ->assertDontSee('Beasiswa Seni Musik');

        // Filter by status
        $statusResponse = $this->actingAs($admin)
            ->get(route('admin.scholarships.index', ['status' => 'draft']));
        $statusResponse->assertOk()
            ->assertSee('Beasiswa Seni Musik')
            ->assertDontSee('Beasiswa Robotika AI');
    }
}
