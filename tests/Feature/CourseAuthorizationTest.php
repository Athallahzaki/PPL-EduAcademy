<?php

namespace Tests\Feature;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructor_cannot_access_another_instructors_class(): void
    {
        $instructor1 = User::factory()->instructor()->active()->create();
        $instructor2 = User::factory()->instructor()->active()->create();

        $course = Course::factory()->create([
            'instructor_id' => $instructor1->id,
            'status' => CourseStatus::Published,
        ]);

        // Instructor 2 attempts access to Instructor 1's course
        $this->actingAs($instructor2)->get(route('instructor.courses.show', $course))->assertStatus(403);
        $this->actingAs($instructor2)->get(route('instructor.courses.edit', $course))->assertStatus(403);
        $this->actingAs($instructor2)->put(route('instructor.courses.update', $course), [
            'title' => 'Hacked Title',
            'description' => 'Valid description with more than twenty characters.',
            'subject_id' => $course->subject_id,
            'level' => CourseLevel::Beginner->value,
        ])->assertStatus(403);
        $this->actingAs($instructor2)->patch(route('instructor.courses.status', $course), [
            'status' => CourseStatus::Archived->value,
        ])->assertStatus(403);
        $this->actingAs($instructor2)->delete(route('instructor.courses.destroy', $course))->assertStatus(403);
        $this->actingAs($instructor2)->get(route('instructor.courses.students', $course))->assertStatus(403);
    }

    public function test_student_and_admin_cannot_access_instructor_courses_routes(): void
    {
        $student = User::factory()->student()->active()->create();
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        // Student forbidden on instructor routes
        $this->actingAs($student)->get(route('instructor.courses.index'))->assertStatus(403);
        $this->actingAs($student)->get(route('instructor.courses.create'))->assertStatus(403);
        $this->actingAs($student)->get(route('instructor.courses.show', $course))->assertStatus(403);

        // Admin forbidden on instructor routes (§1.10 / §4.1)
        $this->actingAs($admin)->get(route('instructor.courses.index'))->assertStatus(403);
        $this->actingAs($admin)->get(route('instructor.courses.create'))->assertStatus(403);
        $this->actingAs($admin)->get(route('instructor.courses.show', $course))->assertStatus(403);
    }

    public function test_instructor_and_admin_cannot_access_student_course_routes(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->published()->create();

        // Instructor forbidden on student routes
        $this->actingAs($instructor)->get(route('student.catalog.index'))->assertStatus(403);
        $this->actingAs($instructor)->get(route('student.courses.index'))->assertStatus(403);

        // Admin forbidden on student routes
        $this->actingAs($admin)->get(route('student.catalog.index'))->assertStatus(403);
        $this->actingAs($admin)->get(route('student.courses.index'))->assertStatus(403);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $course = Course::factory()->published()->create();

        $this->get(route('instructor.courses.index'))->assertRedirect(route('login'));
        $this->get(route('student.catalog.index'))->assertRedirect(route('login'));
        $this->get(route('student.courses.index'))->assertRedirect(route('login'));
        $this->get(route('student.courses.show', $course))->assertRedirect(route('login'));
    }

    public function test_mass_assignment_protection_for_instructor_id_and_status(): void
    {
        $instructor1 = User::factory()->instructor()->active()->create();
        $instructor2 = User::factory()->instructor()->active()->create();
        $subject = Subject::factory()->create();

        // Attempt privilege escalation during creation
        $response = $this->actingAs($instructor1)->post(route('instructor.courses.store'), [
            'instructor_id' => $instructor2->id,
            'status' => CourseStatus::Published->value,
            'title' => 'Exploit Test Course',
            'description' => 'A valid test course description exceeding twenty characters.',
            'subject_id' => $subject->id,
            'level' => CourseLevel::Beginner->value,
        ]);

        $created = Course::where('title', 'Exploit Test Course')->first();
        $this->assertNotNull($created);
        $this->assertSame($instructor1->id, $created->instructor_id);
        $this->assertSame(CourseStatus::Draft, $created->status);

        // Attempt status and instructor escalation during update
        $this->actingAs($instructor1)->put(route('instructor.courses.update', $created), [
            'instructor_id' => $instructor2->id,
            'status' => CourseStatus::Published->value,
            'title' => 'Updated Exploit Test Course',
            'description' => 'A valid test course description exceeding twenty characters.',
            'subject_id' => $subject->id,
            'level' => CourseLevel::Intermediate->value,
        ]);

        $created->refresh();
        $this->assertSame($instructor1->id, $created->instructor_id);
        $this->assertSame(CourseStatus::Draft, $created->status);
    }
}

