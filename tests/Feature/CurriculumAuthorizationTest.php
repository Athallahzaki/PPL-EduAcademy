<?php

namespace Tests\Feature;

use App\Enums\ChapterType;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructor_cannot_manage_another_instructors_curriculum(): void
    {
        $instructor1 = User::factory()->instructor()->active()->create();
        $instructor2 = User::factory()->instructor()->active()->create();

        $course = Course::factory()->create(['instructor_id' => $instructor1->id]);
        $module = Module::factory()->create(['course_id' => $course->id]);
        $chapter = Chapter::factory()->create(['module_id' => $module->id]);

        // Instructor 2 attempts access to Instructor 1's course modules
        $this->actingAs($instructor2)->get(route('instructor.courses.modules.index', $course))->assertStatus(403);
        $this->actingAs($instructor2)->get(route('instructor.courses.modules.create', $course))->assertStatus(403);
        $this->actingAs($instructor2)->post(route('instructor.courses.modules.store', $course), [
            'title' => 'Malicious Module',
        ])->assertStatus(403);
        $this->actingAs($instructor2)->get(route('instructor.courses.modules.edit', [$course, $module]))->assertStatus(403);
        $this->actingAs($instructor2)->put(route('instructor.courses.modules.update', [$course, $module]), [
            'title' => 'Malicious Update',
        ])->assertStatus(403);
        $this->actingAs($instructor2)->delete(route('instructor.courses.modules.destroy', [$course, $module]))->assertStatus(403);
        $this->actingAs($instructor2)->patch(route('instructor.courses.modules.move', [$course, $module]), ['direction' => 'up'])->assertStatus(403);
        $this->actingAs($instructor2)->patch(route('instructor.courses.modules.publish', [$course, $module]), ['is_published' => true])->assertStatus(403);

        // Instructor 2 attempts access to Instructor 1's chapters
        $this->actingAs($instructor2)->get(route('instructor.courses.modules.chapters.create', [$course, $module]))->assertStatus(403);
        $this->actingAs($instructor2)->post(route('instructor.courses.modules.chapters.store', [$course, $module]), [
            'title' => 'Malicious Chapter',
            'type' => ChapterType::Text->value,
            'body' => 'Malicious body',
        ])->assertStatus(403);
        $this->actingAs($instructor2)->get(route('instructor.courses.modules.chapters.show', [$course, $module, $chapter]))->assertStatus(403);
        $this->actingAs($instructor2)->get(route('instructor.courses.modules.chapters.edit', [$course, $module, $chapter]))->assertStatus(403);
        $this->actingAs($instructor2)->put(route('instructor.courses.modules.chapters.update', [$course, $module, $chapter]), [
            'title' => 'Updated Chapter',
            'type' => ChapterType::Text->value,
            'body' => 'Updated body',
        ])->assertStatus(403);
        $this->actingAs($instructor2)->delete(route('instructor.courses.modules.chapters.destroy', [$course, $module, $chapter]))->assertStatus(403);
    }

    public function test_scoped_bindings_prevent_cross_course_and_cross_module_access(): void
    {
        $instructor = User::factory()->instructor()->active()->create();

        $courseA = Course::factory()->create(['instructor_id' => $instructor->id]);
        $courseB = Course::factory()->create(['instructor_id' => $instructor->id]);

        $moduleA = Module::factory()->create(['course_id' => $courseA->id]);
        $moduleB = Module::factory()->create(['course_id' => $courseB->id]);

        $chapterA = Chapter::factory()->create(['module_id' => $moduleA->id]);
        $chapterB = Chapter::factory()->create(['module_id' => $moduleB->id]);

        // Accessing moduleB via courseA URL must 404 due to scoped bindings
        $this->actingAs($instructor)->get(route('instructor.courses.modules.edit', [$courseA, $moduleB]))->assertStatus(404);
        $this->actingAs($instructor)->put(route('instructor.courses.modules.update', [$courseA, $moduleB]), ['title' => 'X'])->assertStatus(404);

        // Accessing chapterB via moduleA URL must 404 due to scoped bindings
        $this->actingAs($instructor)->get(route('instructor.courses.modules.chapters.show', [$courseA, $moduleA, $chapterB]))->assertStatus(404);
        $this->actingAs($instructor)->get(route('instructor.courses.modules.chapters.edit', [$courseA, $moduleA, $chapterB]))->assertStatus(404);
    }

    public function test_students_and_admins_are_forbidden_from_instructor_curriculum_routes(): void
    {
        $student = User::factory()->student()->active()->create();
        $admin = User::factory()->admin()->create();

        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);

        // Student forbidden
        $this->actingAs($student)->get(route('instructor.courses.modules.index', $course))->assertStatus(403);
        $this->actingAs($student)->get(route('instructor.courses.modules.create', $course))->assertStatus(403);

        // Admin forbidden
        $this->actingAs($admin)->get(route('instructor.courses.modules.index', $course))->assertStatus(403);
        $this->actingAs($admin)->get(route('instructor.courses.modules.create', $course))->assertStatus(403);
    }

    public function test_non_enrolled_student_cannot_access_student_content_routes(): void
    {
        $student = User::factory()->student()->active()->create();
        $course = Course::factory()->published()->create();
        $module = Module::factory()->published()->create(['course_id' => $course->id]);
        $chapter = Chapter::factory()->published()->create(['module_id' => $module->id]);

        // Student is not enrolled, so authorization fails
        $this->actingAs($student)->get(route('student.courses.modules.index', $course))->assertStatus(403);
        $this->actingAs($student)->get(route('student.courses.modules.chapters.show', [$course, $module, $chapter]))->assertStatus(403);
    }

    public function test_unauthenticated_guests_redirect_to_login(): void
    {
        $course = Course::factory()->published()->create();
        $module = Module::factory()->published()->create(['course_id' => $course->id]);
        $chapter = Chapter::factory()->published()->create(['module_id' => $module->id]);

        $this->get(route('instructor.courses.modules.index', $course))->assertRedirect(route('login'));
        $this->get(route('student.courses.modules.index', $course))->assertRedirect(route('login'));
        $this->get(route('student.courses.modules.chapters.show', [$course, $module, $chapter]))->assertRedirect(route('login'));
    }

    public function test_mass_assignment_protection_on_module_and_chapter(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $otherCourse = Course::factory()->create();

        // Attempting to set course_id, position, is_published in module store
        $this->actingAs($instructor)->post(route('instructor.courses.modules.store', $course), [
            'title' => 'Protected Module',
            'course_id' => $otherCourse->id,
            'position' => 999,
            'is_published' => true,
        ])->assertRedirect(route('instructor.courses.modules.index', $course));

        $createdModule = Module::where('title', 'Protected Module')->first();
        $this->assertNotNull($createdModule);
        $this->assertSame($course->id, $createdModule->course_id);
        $this->assertSame(1, $createdModule->position);
        $this->assertFalse($createdModule->is_published);

        // Attempting to set module_id, position, is_published in chapter store
        $otherModule = Module::factory()->create();
        $this->actingAs($instructor)->post(route('instructor.courses.modules.chapters.store', [$course, $createdModule]), [
            'title' => 'Protected Chapter',
            'type' => ChapterType::Text->value,
            'body' => 'Some content',
            'module_id' => $otherModule->id,
            'position' => 999,
            'is_published' => true,
        ])->assertRedirect(route('instructor.courses.modules.index', $course));

        $createdChapter = Chapter::where('title', 'Protected Chapter')->first();
        $this->assertNotNull($createdChapter);
        $this->assertSame($createdModule->id, $createdChapter->module_id);
        $this->assertSame(1, $createdChapter->position);
        $this->assertFalse($createdChapter->is_published);
    }
}
