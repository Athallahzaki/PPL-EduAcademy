<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\Scholarship;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards the shared UI conventions: sticky layout, standard list-page
 * components (chips, filter bar, row actions, badges) and English UI text.
 */
class UiConsistencyTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create(['must_change_password' => false]);
    }

    public function test_layout_keeps_sidebar_and_top_bar_sticky(): void
    {
        $html = $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('lg:sticky', $html);
        $this->assertStringContainsString('lg:h-screen', $html);
        $this->assertStringContainsString('sticky top-0 z-30', $html);
    }

    public function test_filter_bar_shows_reset_only_when_filters_are_active(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk()->assertDontSee('Reset filters');
        $this->actingAs($admin)->get(route('admin.users.index', ['q' => 'abc']))->assertOk()->assertSee('Reset filters');
        $this->actingAs($admin)->get(route('admin.scholarships.index', ['q' => 'abc']))->assertOk()->assertSee('Reset filters');
        $this->actingAs($admin)->get(route('admin.announcements.index', ['status' => 'active']))->assertOk()->assertSee('Reset filters');
    }

    public function test_scholarship_list_uses_status_chips_badges_and_row_actions(): void
    {
        Scholarship::factory()->published()->create(['title' => 'Alpha Award']);
        Scholarship::factory()->draft()->create(['title' => 'Beta Award']);
        Scholarship::factory()->closed()->create(['title' => 'Gamma Award']);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.scholarships.index'))
            ->assertOk()
            ->assertSee('Published (1)', false)
            ->assertSee('Draft (1)', false)
            ->assertSee('Closed (1)', false)
            ->getContent();

        // Closed status renders with the rose badge variant (it used to fall back to gray).
        $this->assertStringContainsString('bg-rose-50 text-rose-700', $html);
        $this->assertStringContainsString('>View</a>', $html);
        $this->assertStringContainsString('>Edit</a>', $html);
        $this->assertStringContainsString('>Delete</button>', $html);
        $this->assertStringContainsString('Delete this scholarship?', $html);

        $this->actingAs($this->admin())
            ->get(route('admin.scholarships.index', ['status' => 'closed']))
            ->assertSee('Gamma Award')
            ->assertDontSee('Alpha Award');
    }

    public function test_announcement_list_shows_status_chips_and_badges(): void
    {
        $admin = $this->admin();
        foreach ([true, true, false] as $i => $active) {
            Announcement::create([
                'admin_id' => $admin->id,
                'title' => 'Notice '.$i,
                'content' => 'Body text '.$i,
                'is_active' => $active,
                'published_at' => now(),
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.announcements.index'))
            ->assertOk()
            ->assertSee('All (3)', false)
            ->assertSee('Active (2)', false)
            ->assertSee('Inactive (1)', false);
    }

    public function test_role_badges_use_consistent_colours(): void
    {
        User::factory()->instructor()->active()->create(['name' => 'Ina Instructor']);

        $this->actingAs($this->admin())
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('bg-purple-50 text-purple-700', false);
    }

    public function test_instructor_course_list_uses_chips_and_row_actions(): void
    {
        $instructor = User::factory()->instructor()->active()->create([
            'onboarded_at' => now(),
            'must_change_password' => false,
        ]);
        Course::factory()->published()->create(['instructor_id' => $instructor->id, 'title' => 'Chip Course']);

        $this->actingAs($instructor)
            ->get(route('instructor.courses.index'))
            ->assertOk()
            ->assertSee('Published (1)', false)
            ->assertSee('Chip Course')
            ->assertSee('Manage');
    }

    public function test_flash_message_is_rendered_once_on_forum_pages(): void
    {
        $student = User::factory()->student()->active()->create([
            'onboarded_at' => now(),
            'must_change_password' => false,
        ]);
        $subject = Subject::factory()->create();

        $response = $this->actingAs($student)
            ->followingRedirects()
            ->post(route('forum.threads.store'), [
                'title' => 'A brand new topic',
                'subject_id' => $subject->id,
                'content' => 'This is the body of the new topic.',
            ]);

        $response->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), 'Discussion topic published successfully.'));
    }

    public function test_ui_text_is_english_on_forum_scholarship_and_profile_pages(): void
    {
        $admin = $this->admin();
        $scholarship = Scholarship::factory()->published()->create();
        $student = User::factory()->student()->active()->create([
            'onboarded_at' => now(),
            'must_change_password' => false,
        ]);
        $instructor = User::factory()->instructor()->active()->create([
            'onboarded_at' => now(),
            'must_change_password' => false,
        ]);

        $pages = [
            [$admin, route('admin.scholarships.index')],
            [$admin, route('admin.scholarships.create')],
            [$admin, route('admin.scholarships.show', $scholarship)],
            [$admin, route('admin.scholarships.edit', $scholarship)],
            [$student, route('forum.threads.index')],
            [$student, route('forum.threads.create')],
            [$student, route('student.dashboard')],
            [$instructor, route('instructor.profile.show')],
            [$instructor, route('instructor.profile.edit')],
        ];

        $indonesian = ['Simpan', 'Batal', 'Hapus', 'Kembali', 'Ubah', 'Pulihkan', 'Beasiswa', 'Topik', 'Diskusi', 'Kategori', 'Cari', 'Judul', 'Profil', 'Unduh'];

        foreach ($pages as [$user, $url]) {
            $html = strip_tags($this->actingAs($user)->get($url)->assertOk()->getContent());
            foreach ($indonesian as $word) {
                $this->assertSame(0, preg_match('/\b'.$word.'\b/', $html), "Found '$word' on $url");
            }
        }
    }
}
