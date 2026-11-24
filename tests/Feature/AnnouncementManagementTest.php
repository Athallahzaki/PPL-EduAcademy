<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_announcement(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Scheduled maintenance',
            'content' => 'Service is unavailable tonight.',
            'image_url' => 'https://example.com/maintenance.png',
            'is_active' => '1',
        ]);

        $announcement = Announcement::query()->firstOrFail();
        $response->assertRedirect(route('admin.announcements.index'))
            ->assertSessionHas('success');
        $this->assertSame($admin->id, $announcement->admin_id);
        $this->assertTrue($announcement->is_active);
        $this->assertNotNull($announcement->published_at);
        $this->assertSame('https://example.com/maintenance.png', $announcement->image_url);

        $this->actingAs($admin)
            ->put(route('admin.announcements.update', $announcement), [
                'title' => 'Updated notice',
                'content' => 'Updated message.',
                'image_url' => '',
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.announcements.index'))
            ->assertSessionHas('success');

        $this->assertSame('Updated notice', $announcement->fresh()->title);
        $this->assertFalse($announcement->fresh()->is_active);

        $this->actingAs($admin)
            ->delete(route('admin.announcements.destroy', $announcement))
            ->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_announcement_requests_are_limited_to_admins(): void
    {
        $student = User::factory()->student()->active()->create();

        $this->actingAs($student)
            ->get(route('admin.announcements.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->post(route('admin.announcements.store'), [])
            ->assertForbidden();
    }

    public function test_invalid_announcement_data_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.announcements.create'))
            ->post(route('admin.announcements.store'), [
                'title' => '',
                'content' => '',
                'image_url' => 'not-a-url',
                'is_active' => 'invalid',
            ])
            ->assertSessionHasErrors(['title', 'content', 'image_url', 'is_active']);
    }

    public function test_only_current_active_published_announcements_are_shown_to_students_and_instructors(): void
    {
        $admin = User::factory()->admin()->create();

        Announcement::create([
            'admin_id' => $admin->id,
            'title' => 'Visible announcement',
            'content' => 'Published content',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);
        Announcement::create([
            'admin_id' => $admin->id,
            'title' => 'Inactive announcement',
            'content' => 'Hidden content',
            'is_active' => false,
            'published_at' => now()->subMinute(),
        ]);
        Announcement::create([
            'admin_id' => $admin->id,
            'title' => 'Future announcement',
            'content' => 'Not yet published',
            'is_active' => true,
            'published_at' => now()->addDay(),
        ]);
        Announcement::create([
            'admin_id' => $admin->id,
            'title' => 'Archived announcement',
            'content' => 'Older than six months',
            'is_active' => true,
            'published_at' => now()->subMonths(6)->subDay(),
        ]);

        $student = User::factory()->student()->active()->create();
        $this->actingAs($student)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Visible announcement')
            ->assertDontSee('Inactive announcement')
            ->assertDontSee('Future announcement')
            ->assertDontSee('Archived announcement');

        $instructor = User::factory()->instructor()->active()->create();
        $this->actingAs($instructor)
            ->get(route('instructor.dashboard'))
            ->assertOk()
            ->assertSee('Visible announcement')
            ->assertDontSee('Inactive announcement')
            ->assertDontSee('Future announcement')
            ->assertDontSee('Archived announcement');
    }

    public function test_admin_can_filter_announcements_by_author_period_and_status(): void
    {
        $adminOne = User::factory()->admin()->create(['name' => 'Admin One']);
        $adminTwo = User::factory()->admin()->create(['name' => 'Admin Two']);

        $announcements = [
            ['title' => 'Seven day item', 'admin' => $adminOne, 'days' => 3, 'active' => true],
            ['title' => 'Thirty day item', 'admin' => $adminOne, 'days' => 20, 'active' => false],
            ['title' => 'Three month item', 'admin' => $adminTwo, 'days' => 60, 'active' => true],
            ['title' => 'Six month item', 'admin' => $adminTwo, 'days' => 120, 'active' => true],
            ['title' => 'Archived item', 'admin' => $adminTwo, 'days' => 190, 'active' => true],
        ];

        foreach ($announcements as $item) {
            Announcement::create([
                'admin_id' => $item['admin']->id,
                'title' => $item['title'],
                'content' => $item['title'],
                'is_active' => $item['active'],
                'published_at' => now()->subDays($item['days']),
            ]);
        }

        $this->actingAs($adminOne)
            ->get(route('admin.announcements.index', ['admin_id' => $adminTwo->id]))
            ->assertOk()
            ->assertSee('Three month item')
            ->assertDontSee('Seven day item');

        foreach ([
            '7_days' => ['Seven day item'],
            '30_days' => ['Seven day item', 'Thirty day item'],
            '3_months' => ['Seven day item', 'Thirty day item', 'Three month item'],
            '6_months' => ['Seven day item', 'Thirty day item', 'Three month item', 'Six month item'],
        ] as $period => $visibleTitles) {
            $response = $this->actingAs($adminOne)
                ->get(route('admin.announcements.index', ['period' => $period]))
                ->assertOk();

            foreach ($visibleTitles as $title) {
                $response->assertSee($title);
            }

            $response->assertDontSee('Archived item');
        }

        $this->actingAs($adminOne)
            ->get(route('admin.announcements.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee('Thirty day item')
            ->assertDontSee('Seven day item');

        $this->actingAs($adminOne)
            ->get(route('admin.announcements.index'))
            ->assertSee('Archived item')
            ->assertSee('Archived');
    }

    public function test_announcement_filters_reject_invalid_values(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.announcements.index', [
                'admin_id' => 'not-an-id',
                'period' => 'forever',
                'status' => 'scheduled',
            ]))
            ->assertSessionHasErrors(['admin_id', 'period', 'status']);
    }
}
