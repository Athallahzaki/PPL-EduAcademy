<?php

namespace Tests\Feature;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstructorCourseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_creation_validations_and_default_draft_state(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $subject = Subject::factory()->create();

        // 1. Validation failure: Short description (<20 chars), invalid subject, invalid level
        $responseFail = $this->actingAs($instructor)->post(route('instructor.courses.store'), [
            'title' => '',
            'description' => 'Too short',
            'subject_id' => 99999,
            'level' => 'expert',
        ]);
        $responseFail->assertSessionHasErrors(['title', 'description', 'subject_id', 'level']);

        // 2. Successful creation
        $responseSuccess = $this->actingAs($instructor)->post(route('instructor.courses.store'), [
            'title' => 'Valid Web Development Class',
            'description' => 'This is a comprehensive description exceeding twenty characters.',
            'subject_id' => $subject->id,
            'level' => CourseLevel::Beginner->value,
        ]);

        $course = Course::where('title', 'Valid Web Development Class')->first();
        $this->assertNotNull($course);
        $this->assertSame(CourseStatus::Draft, $course->status);
        $this->assertSame($instructor->id, $course->instructor_id);
        $responseSuccess->assertRedirect(route('instructor.courses.show', $course));
        $responseSuccess->assertSessionHas('success');
    }

    public function test_banner_upload_validation_and_storage_lifecycle(): void
    {
        Storage::fake('public');

        $instructor = User::factory()->instructor()->active()->create();
        $subject = Subject::factory()->create();

        // 1. Invalid file: SVG rejected, GIF rejected, oversized rejected
        $svgFile = UploadedFile::fake()->create('vector.svg', 100, 'image/svg+xml');
        $oversizedFile = UploadedFile::fake()->create('huge.png', 3000, 'image/png');

        $resSvg = $this->actingAs($instructor)->post(route('instructor.courses.store'), [
            'title' => 'SVG Banner Course',
            'description' => 'Valid description exceeding the minimum twenty characters requirement.',
            'subject_id' => $subject->id,
            'level' => CourseLevel::Beginner->value,
            'banner' => $svgFile,
        ]);
        $resSvg->assertSessionHasErrors('banner');

        $resOversized = $this->actingAs($instructor)->post(route('instructor.courses.store'), [
            'title' => 'Oversized Banner Course',
            'description' => 'Valid description exceeding the minimum twenty characters requirement.',
            'subject_id' => $subject->id,
            'level' => CourseLevel::Beginner->value,
            'banner' => $oversizedFile,
        ]);
        $resOversized->assertSessionHasErrors('banner');

        // 2. Valid banner: PNG <= 2MB
        $validFile = UploadedFile::fake()->image('banner.png', 800, 450)->size(500);
        $resValid = $this->actingAs($instructor)->post(route('instructor.courses.store'), [
            'title' => 'Valid Banner Course',
            'description' => 'Valid description exceeding the minimum twenty characters requirement.',
            'subject_id' => $subject->id,
            'level' => CourseLevel::Beginner->value,
            'banner' => $validFile,
        ]);

        $course = Course::where('title', 'Valid Banner Course')->first();
        $this->assertNotNull($course->banner_path);
        Storage::disk('public')->assertExists($course->banner_path);
        $oldBannerPath = $course->banner_path;

        // 3. Update banner: Replacing deletes old banner
        $newFile = UploadedFile::fake()->image('new-banner.jpg', 800, 450)->size(600);
        $this->actingAs($instructor)->put(route('instructor.courses.update', $course), [
            'title' => 'Updated Banner Course',
            'description' => 'Valid description exceeding the minimum twenty characters requirement.',
            'subject_id' => $subject->id,
            'level' => CourseLevel::Beginner->value,
            'banner' => $newFile,
        ]);

        $course->refresh();
        Storage::disk('public')->assertMissing($oldBannerPath);
        Storage::disk('public')->assertExists($course->banner_path);
        $replacedBannerPath = $course->banner_path;

        // 4. Remove banner
        $this->actingAs($instructor)->put(route('instructor.courses.update', $course), [
            'title' => 'Updated Banner Course',
            'description' => 'Valid description exceeding the minimum twenty characters requirement.',
            'subject_id' => $subject->id,
            'level' => CourseLevel::Beginner->value,
            'remove_banner' => '1',
        ]);

        $course->refresh();
        $this->assertNull($course->banner_path);
        Storage::disk('public')->assertMissing($replacedBannerPath);
    }

    public function test_course_status_transitions(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $course = Course::factory()->create([
            'instructor_id' => $instructor->id,
            'status' => CourseStatus::Draft,
        ]);

        // 1. Invalid transition: Draft -> Archived (fails)
        $this->actingAs($instructor)->patch(route('instructor.courses.status', $course), [
            'status' => CourseStatus::Archived->value,
        ])->assertSessionHasErrors('status');
        $this->assertSame(CourseStatus::Draft, $course->fresh()->status);

        // 2. Draft -> Published (succeeds)
        $this->actingAs($instructor)->patch(route('instructor.courses.status', $course), [
            'status' => CourseStatus::Published->value,
        ])->assertSessionHas('success');
        $this->assertSame(CourseStatus::Published, $course->fresh()->status);

        // 3. Published -> Draft (succeeds when 0 enrollments)
        $this->actingAs($instructor)->patch(route('instructor.courses.status', $course), [
            'status' => CourseStatus::Draft->value,
        ])->assertSessionHas('success');
        $this->assertSame(CourseStatus::Draft, $course->fresh()->status);

        // Republish
        $this->actingAs($instructor)->patch(route('instructor.courses.status', $course), [
            'status' => CourseStatus::Published->value,
        ]);

        // 4. Published -> Draft (fails when students are enrolled)
        $student = User::factory()->student()->active()->create();
        Enrollment::create(['course_id' => $course->id, 'student_id' => $student->id]);

        $resEnrolledDraft = $this->actingAs($instructor)->patch(route('instructor.courses.status', $course), [
            'status' => CourseStatus::Draft->value,
        ]);
        $resEnrolledDraft->assertSessionHasErrors('status');
        $this->assertSame(CourseStatus::Published, $course->fresh()->status);

        // 5. Published -> Archived (succeeds)
        $this->actingAs($instructor)->patch(route('instructor.courses.status', $course), [
            'status' => CourseStatus::Archived->value,
        ])->assertSessionHas('success');
        $this->assertSame(CourseStatus::Archived, $course->fresh()->status);

        // 6. Archived -> Published (republish succeeds)
        $this->actingAs($instructor)->patch(route('instructor.courses.status', $course), [
            'status' => CourseStatus::Published->value,
        ])->assertSessionHas('success');
        $this->assertSame(CourseStatus::Published, $course->fresh()->status);
    }

    public function test_delete_course_rule_and_banner_cleanup(): void
    {
        Storage::fake('public');

        $instructor = User::factory()->instructor()->active()->create();
        $bannerFile = UploadedFile::fake()->image('banner.jpg');
        $path = $bannerFile->store('course-banners', 'public');

        $course = Course::factory()->create([
            'instructor_id' => $instructor->id,
            'status' => CourseStatus::Draft,
            'banner_path' => $path,
        ]);

        // When enrollments exist, delete is denied with specific message (§4.1 / §12)
        $student = User::factory()->student()->active()->create();
        $enrollment = Enrollment::create(['course_id' => $course->id, 'student_id' => $student->id]);

        $responseDenied = $this->actingAs($instructor)->delete(route('instructor.courses.destroy', $course));
        $responseDenied->assertStatus(403);
        $this->assertNotSoftDeleted('courses', ['id' => $course->id]);
        Storage::disk('public')->assertExists($path);

        // Remove enrollment so enrollments === 0
        $enrollment->delete();

        $responseAllowed = $this->actingAs($instructor)->delete(route('instructor.courses.destroy', $course));
        $responseAllowed->assertRedirect(route('instructor.courses.index'));
        $this->assertSoftDeleted('courses', ['id' => $course->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_students_tab_lists_only_enrolled_students_and_supports_search(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $course = Course::factory()->published()->create(['instructor_id' => $instructor->id]);

        $student1 = User::factory()->student()->active()->create(['name' => 'Alice Cooper', 'email' => 'alice@test.com']);
        $student2 = User::factory()->student()->active()->create(['name' => 'Bob Marley', 'email' => 'bob@test.com']);
        $uninvolvedStudent = User::factory()->student()->active()->create(['name' => 'Charlie Brown']);

        Enrollment::create(['course_id' => $course->id, 'student_id' => $student1->id]);
        Enrollment::create(['course_id' => $course->id, 'student_id' => $student2->id]);

        // Default view shows both enrolled students and does not show uninvolved students
        $response = $this->actingAs($instructor)->get(route('instructor.courses.students', $course));
        $response->assertSee('Alice Cooper');
        $response->assertSee('Bob Marley');
        $response->assertDontSee('Charlie Brown');

        // Search query filter
        $responseSearch = $this->actingAs($instructor)->get(route('instructor.courses.students', [$course, 'q' => 'Alice']));
        $responseSearch->assertSee('Alice Cooper');
        $responseSearch->assertDontSee('Bob Marley');
    }
}

