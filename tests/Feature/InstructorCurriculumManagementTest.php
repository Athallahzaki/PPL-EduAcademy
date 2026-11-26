<?php

namespace Tests\Feature;

use App\Enums\ChapterType;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorCurriculumManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructor_can_create_module_with_validation(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Validation error on empty title
        $this->actingAs($instructor)
            ->post(route('instructor.courses.modules.store', $course), [
                'title' => '',
            ])
            ->assertSessionHasErrors('title');

        // Successful creation starts as draft and appended at end
        $response = $this->actingAs($instructor)
            ->post(route('instructor.courses.modules.store', $course), [
                'title' => 'First Module',
                'description' => 'Introduction description',
            ]);

        $response->assertRedirect(route('instructor.courses.modules.index', $course));
        $this->assertDatabaseHas('modules', [
            'course_id' => $course->id,
            'title' => 'First Module',
            'description' => 'Introduction description',
            'position' => 1,
            'is_published' => false,
        ]);
    }

    public function test_instructor_can_create_text_and_video_chapters_with_type_rules(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $module = Module::factory()->create(['course_id' => $course->id]);

        // 1. Text chapter validation: body is required
        $this->actingAs($instructor)
            ->post(route('instructor.courses.modules.chapters.store', [$course, $module]), [
                'title' => 'Empty Text Chapter',
                'type' => ChapterType::Text->value,
                'body' => '',
            ])
            ->assertSessionHasErrors('body');

        // 2. Video chapter validation: video_url is required
        $this->actingAs($instructor)
            ->post(route('instructor.courses.modules.chapters.store', [$course, $module]), [
                'title' => 'Empty Video Chapter',
                'type' => ChapterType::Video->value,
                'video_url' => '',
            ])
            ->assertSessionHasErrors('video_url');

        // 3. Creating text chapter clears video_url even if sent
        $this->actingAs($instructor)
            ->post(route('instructor.courses.modules.chapters.store', [$course, $module]), [
                'title' => 'Valid Text Chapter',
                'type' => ChapterType::Text->value,
                'body' => '# Heading\n\nContent here',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->assertRedirect(route('instructor.courses.modules.index', $course));

        $textChapter = Chapter::where('title', 'Valid Text Chapter')->first();
        $this->assertNotNull($textChapter);
        $this->assertNull($textChapter->video_url);
        $this->assertSame(1, $textChapter->position);
        $this->assertFalse($textChapter->is_published);

        // 4. Creating video chapter with optional notes
        $this->actingAs($instructor)
            ->post(route('instructor.courses.modules.chapters.store', [$course, $module]), [
                'title' => 'Valid Video Chapter',
                'type' => ChapterType::Video->value,
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'body' => 'Optional video notes',
            ])
            ->assertRedirect(route('instructor.courses.modules.index', $course));

        $videoChapter = Chapter::where('title', 'Valid Video Chapter')->first();
        $this->assertNotNull($videoChapter);
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $videoChapter->video?->embedUrl);
        $this->assertSame(2, $videoChapter->position);
    }

    public function test_reordering_modules_and_chapters_keeps_contiguous_positions(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        $mod1 = Module::factory()->create(['course_id' => $course->id, 'title' => 'M1', 'position' => 1]);
        $mod2 = Module::factory()->create(['course_id' => $course->id, 'title' => 'M2', 'position' => 2]);
        $mod3 = Module::factory()->create(['course_id' => $course->id, 'title' => 'M3', 'position' => 3]);

        // Moving first module up is a no-op
        $this->actingAs($instructor)
            ->patch(route('instructor.courses.modules.move', [$course, $mod1]), ['direction' => 'up'])
            ->assertRedirect();
        $this->assertSame(1, $mod1->fresh()->position);

        // Moving mod2 down swaps with mod3
        $this->actingAs($instructor)
            ->patch(route('instructor.courses.modules.move', [$course, $mod2]), ['direction' => 'down'])
            ->assertRedirect();

        $this->assertSame(1, $mod1->fresh()->position);
        $this->assertSame(3, $mod2->fresh()->position);
        $this->assertSame(2, $mod3->fresh()->position);

        // Delete middle module (mod3 at position 2) re-numbers remaining siblings
        $this->actingAs($instructor)
            ->delete(route('instructor.courses.modules.destroy', [$course, $mod3]))
            ->assertRedirect(route('instructor.courses.modules.index', $course));

        $this->assertDatabaseMissing('modules', ['id' => $mod3->id]);
        $this->assertSame(1, $mod1->fresh()->position);
        $this->assertSame(2, $mod2->fresh()->position);
    }

    public function test_publishing_and_unpublishing_modules_and_chapters(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $module = Module::factory()->create(['course_id' => $course->id, 'is_published' => false]);
        $chapter = Chapter::factory()->create(['module_id' => $module->id, 'is_published' => false]);

        // Publish module
        $this->actingAs($instructor)
            ->patch(route('instructor.courses.modules.publish', [$course, $module]), ['is_published' => true])
            ->assertRedirect();
        $this->assertTrue($module->fresh()->is_published);

        // Publish chapter
        $this->actingAs($instructor)
            ->patch(route('instructor.courses.modules.chapters.publish', [$course, $module, $chapter]), ['is_published' => true])
            ->assertRedirect();
        $this->assertTrue($chapter->fresh()->is_published);

        // Unpublish module
        $this->actingAs($instructor)
            ->patch(route('instructor.courses.modules.publish', [$course, $module]), ['is_published' => false])
            ->assertRedirect();
        $this->assertFalse($module->fresh()->is_published);
    }

    public function test_deleting_module_cascades_to_chapters(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $module = Module::factory()->create(['course_id' => $course->id]);
        $chapter = Chapter::factory()->create(['module_id' => $module->id]);

        $this->actingAs($instructor)
            ->delete(route('instructor.courses.modules.destroy', [$course, $module]))
            ->assertRedirect(route('instructor.courses.modules.index', $course));

        $this->assertDatabaseMissing('modules', ['id' => $module->id]);
        $this->assertDatabaseMissing('chapters', ['id' => $chapter->id]);
    }

    public function test_instructor_preview_shows_draft_chapter(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $module = Module::factory()->create(['course_id' => $course->id, 'is_published' => false]);
        $chapter = Chapter::factory()->create([
            'module_id' => $module->id,
            'title' => 'Secret Draft Lesson',
            'is_published' => false,
        ]);

        $response = $this->actingAs($instructor)
            ->get(route('instructor.courses.modules.chapters.show', [$course, $module, $chapter]));

        $response->assertStatus(200);
        $response->assertSee('Secret Draft Lesson');
        $response->assertSee('Instructor Preview Mode');
    }
}
