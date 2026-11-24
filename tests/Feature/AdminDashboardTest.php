<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create(['must_change_password' => false]);
    }

    public function test_dashboard_counts_only_published_classes(): void
    {
        Course::factory()->published()->count(2)->create();
        Course::factory()->draft()->create();
        Course::factory()->archived()->create();

        $response = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Active Classes');

        $this->assertSame(2, $response->viewData('publishedCourses'));
    }

    public function test_progress_card_lists_instructor_profile_pbi(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('PBI-04')
            ->assertSee('Instructor Profile');
    }
}
