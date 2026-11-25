<?php

namespace Tests\Feature;

use App\Enums\ChapterType;
use App\Enums\CourseStatus;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentCourseContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_outline_shows_only_published_modules_and_chapters(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $student = User::factory()->student()->active()->create();
        $course = Course::factory()->published()->create(['instructor_id' => $instructor->id]);

        Enrollment::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'enrolled_at' => now(),
        ]);

        // Module 1: Published
        $module1 = Module::factory()->published()->create([
            'course_id' => $course->id,
            'title' => 'Published Module 1',
            'position' => 1,
        ]);
        $ch1 = Chapter::factory()->text()->published()->create([
            'module_id' => $module1->id,
            'title' => 'Published Chapter 1.1',
            'position' => 1,
        ]);
        $ch2 = Chapter::factory()->text()->create([
            'module_id' => $module1->id,
            'title' => 'Draft Chapter 1.2',
            'position' => 2,
            'is_published' => false,
        ]);

        // Module 2: Draft
        $module2 = Module::factory()->create([
            'course_id' => $course->id,
            'title' => 'Draft Module 2',
            'position' => 2,
            'is_published' => false,
        ]);
        $ch3 = Chapter::factory()->text()->published()->create([
            'module_id' => $module2->id,
            'title' => 'Published Chapter Inside Draft Module',
            'position' => 1,
        ]);

        $response = $this->actingAs($student)->get(route('student.courses.modules.index', $course));

        $response->assertStatus(200);
        $response->assertSee('Published Module 1');
        $response->assertSee('Published Chapter 1.1');
        $response->assertDontSee('Draft Chapter 1.2');
        $response->assertDontSee('Draft Module 2');
        $response->assertDontSee('Published Chapter Inside Draft Module');
        $response->assertSee('Start Learning');
    }

    public function test_student_chapter_page_access_and_404_for_drafts(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $student = User::factory()->student()->active()->create();
        $course = Course::factory()->published()->create(['instructor_id' => $instructor->id]);

        Enrollment::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'enrolled_at' => now(),
        ]);

        $publishedModule = Module::factory()->published()->create(['course_id' => $course->id]);
        $draftModule = Module::factory()->create(['course_id' => $course->id, 'is_published' => false]);

        $publishedChapter = Chapter::factory()->text()->published()->create([
            'module_id' => $publishedModule->id,
            'title' => 'Visible Lesson',
            'body' => "# Title\n\nLesson content with [Link](https://example.com)",
        ]);

        $draftChapter = Chapter::factory()->text()->create([
            'module_id' => $publishedModule->id,
            'is_published' => false,
        ]);

        $chapterInDraftModule = Chapter::factory()->text()->published()->create([
            'module_id' => $draftModule->id,
        ]);

        // 1. Published chapter in published module works
        $this->actingAs($student)
            ->get(route('student.courses.modules.chapters.show', [$course, $publishedModule, $publishedChapter]))
            ->assertStatus(200)
            ->assertSee('Visible Lesson');

        // 2. Draft chapter returns 404
        $this->actingAs($student)
            ->get(route('student.courses.modules.chapters.show', [$course, $publishedModule, $draftChapter]))
            ->assertStatus(404);

        // 3. Published chapter in draft module returns 404
        $this->actingAs($student)
            ->get(route('student.courses.modules.chapters.show', [$course, $draftModule, $chapterInDraftModule]))
            ->assertStatus(404);
    }

    public function test_previous_and_next_navigation_follows_reading_order_across_modules(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $student = User::factory()->student()->active()->create();
        $course = Course::factory()->published()->create(['instructor_id' => $instructor->id]);

        Enrollment::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'enrolled_at' => now(),
        ]);

        $mod1 = Module::factory()->published()->create(['course_id' => $course->id, 'position' => 1]);
        $c1 = Chapter::factory()->text()->published()->create(['module_id' => $mod1->id, 'title' => 'Chap 1', 'position' => 1]);
        $c2 = Chapter::factory()->text()->published()->create(['module_id' => $mod1->id, 'title' => 'Chap 2', 'position' => 2]);

        $mod2 = Module::factory()->published()->create(['course_id' => $course->id, 'position' => 2]);
        $c3 = Chapter::factory()->text()->published()->create(['module_id' => $mod2->id, 'title' => 'Chap 3', 'position' => 1]);

        // Reading Chap 1: has Next (Chap 2), no Previous
        $res1 = $this->actingAs($student)->get(route('student.courses.modules.chapters.show', [$course, $mod1, $c1]));
        $res1->assertStatus(200);
        $res1->assertSee('Chap 2');
        $res1->assertDontSee('Previous:');

        // Reading Chap 2: has Previous (Chap 1) and Next (Chap 3 across module boundary)
        $res2 = $this->actingAs($student)->get(route('student.courses.modules.chapters.show', [$course, $mod1, $c2]));
        $res2->assertStatus(200);
        $res2->assertSee('Previous:');
        $res2->assertSee('Chap 1');
        $res2->assertSee('Next:');
        $res2->assertSee('Chap 3');

        // Reading Chap 3: has Previous (Chap 2), no Next
        $res3 = $this->actingAs($student)->get(route('student.courses.modules.chapters.show', [$course, $mod2, $c3]));
        $res3->assertStatus(200);
        $res3->assertSee('Previous:');
        $res3->assertSee('Chap 2');
        $res3->assertDontSee('Next:');
    }

    public function test_enrolled_student_of_archived_class_can_still_read_content(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $student = User::factory()->student()->active()->create();
        $course = Course::factory()->create([
            'instructor_id' => $instructor->id,
            'status' => CourseStatus::Archived,
        ]);

        Enrollment::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'enrolled_at' => now()->subMonth(),
        ]);

        $mod = Module::factory()->published()->create(['course_id' => $course->id]);
        $chap = Chapter::factory()->text()->published()->create(['module_id' => $mod->id]);

        $this->actingAs($student)
            ->get(route('student.courses.modules.chapters.show', [$course, $mod, $chap]))
            ->assertStatus(200);
    }

    public function test_content_page_queries_stay_bounded(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $student = User::factory()->student()->active()->create();
        $course = Course::factory()->published()->create(['instructor_id' => $instructor->id]);

        Enrollment::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'enrolled_at' => now(),
        ]);

        // Create 3 modules with 3 chapters each
        for ($i = 1; $i <= 3; $i++) {
            $m = Module::factory()->published()->create(['course_id' => $course->id, 'position' => $i]);
            for ($j = 1; $j <= 3; $j++) {
                Chapter::factory()->text()->published()->create(['module_id' => $m->id, 'position' => $j]);
            }
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->actingAs($student)->get(route('student.courses.modules.index', $course))->assertStatus(200);

        $queryCount = count(DB::getQueryLog());
        // Assert no N+1: query count should be well bounded (typically 3-5 queries)
        $this->assertLessThan(10, $queryCount);
    }
}
