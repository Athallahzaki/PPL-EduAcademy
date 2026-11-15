<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CourseOutputSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_description_with_html_and_script_is_escaped(): void
    {
        $instructor = User::factory()->instructor()->active()->create();
        $student = User::factory()->student()->active()->create();

        $xssPayload = "<script>alert('XSS-ATTACK');</script><b>Bold Unsafe</b>";

        $course = Course::factory()->published()->create([
            'instructor_id' => $instructor->id,
            'title' => 'XSS Testing Course',
            'description' => $xssPayload . "\nLine two with normal text.",
        ]);

        Enrollment::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
        ]);

        // 1. Instructor Course Show: verify unescaped script tag is NOT in the response
        $resInst = $this->actingAs($instructor)->get(route('instructor.courses.show', $course));
        $resInst->assertDontSee($xssPayload, false);
        $resInst->assertSee(e($xssPayload), false);

        // 2. Student Course Show
        $resStud = $this->actingAs($student)->get(route('student.courses.show', $course));
        $resStud->assertDontSee($xssPayload, false);
        $resStud->assertSee(e($xssPayload), false);

        // 3. Student Catalog Show
        $resCat = $this->actingAs($student)->get(route('student.catalog.show', $course));
        $resCat->assertRedirect(route('student.courses.show', $course));
    }

    public function test_catalog_and_my_classes_run_bounded_queries_without_n_plus_one(): void
    {
        $student = User::factory()->student()->active()->create();
        $instructor = User::factory()->instructor()->active()->create();

        // Create 15 courses
        for ($i = 0; $i < 15; $i++) {
            $course = Course::factory()->published()->create([
                'instructor_id' => $instructor->id,
                'title' => "Query Optimization Course {$i}",
            ]);

            Enrollment::create([
                'course_id' => $course->id,
                'student_id' => $student->id,
            ]);
        }

        // 1. Catalog query count check (9 items per page)
        DB::enableQueryLog();
        DB::flushQueryLog();

        $this->actingAs($student)->get(route('student.catalog.index'))->assertStatus(200);

        $catalogQueries = count(DB::getQueryLog());
        // Should be bounded (auth user, count query, courses with subject and instructor eager loaded, enrollments pluck)
        $this->assertLessThan(10, $catalogQueries, "Catalog executed too many queries ({$catalogQueries}), potential N+1 query issue.");

        // 2. My Classes query count check
        DB::flushQueryLog();

        $this->actingAs($student)->get(route('student.courses.index'))->assertStatus(200);

        $myClassesQueries = count(DB::getQueryLog());
        $this->assertLessThan(10, $myClassesQueries, "My Classes executed too many queries ({$myClassesQueries}), potential N+1 query issue.");
    }
}

