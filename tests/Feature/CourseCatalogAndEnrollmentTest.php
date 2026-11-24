<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseCatalogAndEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_shows_only_published_classes_with_active_instructors(): void
    {
        $student = User::factory()->student()->active()->create();

        $activeInstructor = User::factory()->instructor()->active()->create();
        $suspendedInstructor = User::factory()->instructor()->suspended()->create();
        $rejectedInstructor = User::factory()->instructor()->rejected()->create();
        $deletedInstructor = User::factory()->instructor()->active()->create();
        $deletedInstructor->delete();

        // 1. Published + Active Instructor -> Visible
        $visibleCourse = Course::factory()->published()->create([
            'instructor_id' => $activeInstructor->id,
            'title' => 'Visible Published Course',
        ]);

        // 2. Draft Course -> Hidden
        $draftCourse = Course::factory()->draft()->create([
            'instructor_id' => $activeInstructor->id,
            'title' => 'Hidden Draft Course',
        ]);

        // 3. Archived Course -> Hidden from catalog
        $archivedCourse = Course::factory()->archived()->create([
            'instructor_id' => $activeInstructor->id,
            'title' => 'Hidden Archived Course',
        ]);

        // 4. Published Course by Suspended Instructor -> Hidden (§1.11 / S2-11)
        $suspendedCourse = Course::factory()->published()->create([
            'instructor_id' => $suspendedInstructor->id,
            'title' => 'Hidden Suspended Instructor Course',
        ]);

        // 5. Published Course by Rejected Instructor -> Hidden
        $rejectedCourse = Course::factory()->published()->create([
            'instructor_id' => $rejectedInstructor->id,
            'title' => 'Hidden Rejected Instructor Course',
        ]);

        // 6. Published Course by Soft-Deleted Instructor -> Hidden
        $deletedCourse = Course::factory()->published()->create([
            'instructor_id' => $deletedInstructor->id,
            'title' => 'Hidden Deleted Instructor Course',
        ]);

        $response = $this->actingAs($student)->get(route('student.catalog.index'));

        $response->assertSee('Visible Published Course');
        $response->assertDontSee('Hidden Draft Course');
        $response->assertDontSee('Hidden Archived Course');
        $response->assertDontSee('Hidden Suspended Instructor Course');
        $response->assertDontSee('Hidden Rejected Instructor Course');
        $response->assertDontSee('Hidden Deleted Instructor Course');
    }

    public function test_reactivating_suspended_instructor_restores_classes_in_catalog(): void
    {
        $student = User::factory()->student()->active()->create();
        $instructor = User::factory()->instructor()->suspended()->create();

        $course = Course::factory()->published()->create([
            'instructor_id' => $instructor->id,
            'title' => 'Reactivating Test Course',
        ]);

        // Initially hidden
        $this->actingAs($student)->get(route('student.catalog.index'))
            ->assertDontSee('Reactivating Test Course');

        // Reactivate instructor
        $instructor->status = AccountStatus::Active;
        $instructor->save();

        // Now visible in catalog (§1.11 / §12)
        $this->actingAs($student)->get(route('student.catalog.index'))
            ->assertSee('Reactivating Test Course');
    }

    public function test_catalog_search_filters_and_pagination(): void
    {
        $student = User::factory()->student()->active()->create();
        $instructor = User::factory()->instructor()->active()->create();

        $subMath = Subject::factory()->create(['name' => 'Mathematics']);
        $subSci = Subject::factory()->create(['name' => 'Science']);

        $mathCourse = Course::factory()->published()->create([
            'instructor_id' => $instructor->id,
            'subject_id' => $subMath->id,
            'title' => 'Calculus I for Engineers',
            'level' => CourseLevel::Intermediate,
        ]);

        $sciCourse = Course::factory()->published()->create([
            'instructor_id' => $instructor->id,
            'subject_id' => $subSci->id,
            'title' => 'Quantum Physics Basics',
            'level' => CourseLevel::Beginner,
        ]);

        // Filter by title search
        $resQ = $this->actingAs($student)->get(route('student.catalog.index', ['q' => 'Calculus']));
        $resQ->assertSee('Calculus I for Engineers');
        $resQ->assertDontSee('Quantum Physics Basics');

        // Filter by subject
        $resSub = $this->actingAs($student)->get(route('student.catalog.index', ['subject' => $subSci->id]));
        $resSub->assertSee('Quantum Physics Basics');
        $resSub->assertDontSee('Calculus I for Engineers');

        // Filter by level
        $resLevel = $this->actingAs($student)->get(route('student.catalog.index', ['level' => 'intermediate']));
        $resLevel->assertSee('Calculus I for Engineers');
        $resLevel->assertDontSee('Quantum Physics Basics');
    }

    public function test_course_detail_page_authorization_and_redirection(): void
    {
        $student = User::factory()->student()->active()->create();
        $instructor = User::factory()->instructor()->active()->create();

        $publishedCourse = Course::factory()->published()->create(['instructor_id' => $instructor->id]);
        $draftCourse = Course::factory()->draft()->create(['instructor_id' => $instructor->id]);
        $archivedCourse = Course::factory()->archived()->create(['instructor_id' => $instructor->id]);

        // 1. Browsable published course: 200 OK
        $this->actingAs($student)->get(route('student.catalog.show', $publishedCourse))->assertStatus(200);

        // 2. Draft / Archived course for non-enrolled student: 404 Not Found (§4.1 / §12)
        $this->actingAs($student)->get(route('student.catalog.show', $draftCourse))->assertStatus(404);
        $this->actingAs($student)->get(route('student.catalog.show', $archivedCourse))->assertStatus(404);

        // 3. If enrolled in archived course: Accessible
        Enrollment::create(['course_id' => $archivedCourse->id, 'student_id' => $student->id]);
        $this->actingAs($student)->get(route('student.courses.show', $archivedCourse))->assertStatus(200);

        // 4. Visiting catalog detail for already enrolled course redirects to student.courses.show (§5 / §7.2)
        Enrollment::create(['course_id' => $publishedCourse->id, 'student_id' => $student->id]);
        $this->actingAs($student)->get(route('student.catalog.show', $publishedCourse))
            ->assertRedirect(route('student.courses.show', $publishedCourse));
    }

    public function test_student_enrollment_flow_and_idempotency(): void
    {
        $student = User::factory()->student()->active()->create();
        $instructor = User::factory()->instructor()->active()->create();

        $course = Course::factory()->published()->create(['instructor_id' => $instructor->id]);

        // 1. First enrollment succeeds
        $response = $this->actingAs($student)->post(route('student.courses.enroll', $course));
        $response->assertRedirect(route('student.courses.show', $course));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('enrollments', [
            'course_id' => $course->id,
            'student_id' => $student->id,
        ]);

        // 2. Double submission (policy catches already enrolled)
        $resDouble = $this->actingAs($student)->post(route('student.courses.enroll', $course));
        $resDouble->assertStatus(403);
        $this->assertSame(1, Enrollment::where('course_id', $course->id)->where('student_id', $student->id)->count());

        // 3. Cannot enroll in draft or archived class
        $draft = Course::factory()->draft()->create();
        $this->actingAs($student)->post(route('student.courses.enroll', $draft))->assertStatus(404);

        $archived = Course::factory()->archived()->create();
        $this->actingAs($student)->post(route('student.courses.enroll', $archived))->assertStatus(404);
    }

    public function test_my_classes_page_and_course_hub_access(): void
    {
        $student = User::factory()->student()->active()->create();
        $activeInstructor = User::factory()->instructor()->active()->create();
        $suspendedInstructor = User::factory()->instructor()->suspended()->create();

        $activeCourse = Course::factory()->published()->create([
            'instructor_id' => $activeInstructor->id,
            'title' => 'Active Enrolled Course',
        ]);

        $suspendedCourse = Course::factory()->published()->create([
            'instructor_id' => $suspendedInstructor->id,
            'title' => 'Suspended Instructor Course',
        ]);

        $archivedCourse = Course::factory()->archived()->create([
            'instructor_id' => $activeInstructor->id,
            'title' => 'Archived Enrolled Course',
        ]);

        $unenrolledCourse = Course::factory()->published()->create([
            'instructor_id' => $activeInstructor->id,
            'title' => 'Unenrolled Course',
        ]);

        Enrollment::create(['course_id' => $activeCourse->id, 'student_id' => $student->id]);
        Enrollment::create(['course_id' => $suspendedCourse->id, 'student_id' => $student->id]);
        Enrollment::create(['course_id' => $archivedCourse->id, 'student_id' => $student->id]);

        // My Classes displays all enrolled courses (including archived & suspended instructor courses §1.11 / S2-11)
        $response = $this->actingAs($student)->get(route('student.courses.index'));
        $response->assertSee('Active Enrolled Course');
        $response->assertSee('Suspended Instructor Course');
        $response->assertSee('Archived Enrolled Course');
        $response->assertDontSee('Unenrolled Course');

        // Hub access: Allowed for enrolled, redirected for unenrolled
        $this->actingAs($student)->get(route('student.courses.show', $activeCourse))->assertStatus(200);
        $this->actingAs($student)->get(route('student.courses.show', $unenrolledCourse))
            ->assertRedirect(route('student.catalog.show', $unenrolledCourse));
    }
}

