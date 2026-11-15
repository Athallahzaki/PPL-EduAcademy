<?php

namespace Database\Seeders;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates demo courses for local environment and initial enrollments (§10).
     */
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $instructor = User::where('email', 'instructor.active@eduacademy.test')->first();
        $student = User::where('email', 'student.active@eduacademy.test')->first();

        if (! $instructor) {
            return;
        }

        $programming = Subject::where('slug', 'programming')->first();
        $design = Subject::where('slug', 'design')->first();

        // 1. Published Course 1
        $publishedCourse1 = Course::updateOrCreate(
            [
                'instructor_id' => $instructor->id,
                'title' => 'Modern Full-Stack Web Development with Laravel & Vue',
            ],
            [
                'subject_id' => $programming?->id ?? 1,
                'description' => "Master modern full-stack web development from scratch. In this comprehensive course, you will learn how to design scalable database architectures, build robust REST and GraphQL APIs using Laravel, and develop reactive, accessible frontend user interfaces.\n\nKey topics covered:\n- Laravel MVC architecture, Service Providers, and Eloquent ORM\n- Modern frontend build pipelines with Vite\n- State management and component architectures\n- Testing with PHPUnit, Pest, and Cypress\n- Production deployment and containerization",
                'level' => CourseLevel::Intermediate,
                'status' => CourseStatus::Published,
                'banner_path' => null,
            ]
        );

        // 2. Published Course 2
        $publishedCourse2 = Course::updateOrCreate(
            [
                'instructor_id' => $instructor->id,
                'title' => 'UI/UX Design Systems & Micro-Interactions',
            ],
            [
                'subject_id' => $design?->id ?? 2,
                'description' => "Learn how to craft accessible, consistent design systems and intuitive user interfaces. Discover atomic design principles, typography hierarchies, fluid grid layouts, and meaningful micro-interactions that elevate digital product experiences.\n\nHighlights:\n- Figma design tokens and component variants\n- Color accessibility (WCAG AA/AAA compliance)\n- Interactive prototyping and motion curves\n- Developer handoff specifications",
                'level' => CourseLevel::Beginner,
                'status' => CourseStatus::Published,
                'banner_path' => null,
            ]
        );

        // 3. Draft Course
        Course::updateOrCreate(
            [
                'instructor_id' => $instructor->id,
                'title' => 'Advanced Algorithmic Problem Solving in Python',
            ],
            [
                'subject_id' => $programming?->id ?? 1,
                'description' => "Deep dive into computational complexity, dynamic programming, graph theory, and advanced data structures. Prepare for technical software engineering interviews at top technology firms.",
                'level' => CourseLevel::Advanced,
                'status' => CourseStatus::Draft,
                'banner_path' => null,
            ]
        );

        // 4. Archived Course
        $archivedCourse = Course::updateOrCreate(
            [
                'instructor_id' => $instructor->id,
                'title' => 'Legacy PHP 7 to 8 Migration Strategies',
            ],
            [
                'subject_id' => $programming?->id ?? 1,
                'description' => "An archived reference class outlining deprecation patterns, type system enhancements, and static analysis workflows for upgrading older PHP codebases.",
                'level' => CourseLevel::Advanced,
                'status' => CourseStatus::Archived,
                'banner_path' => null,
            ]
        );

        // Enroll demo student in one published course and the archived course (§10)
        if ($student) {
            Enrollment::firstOrCreate([
                'course_id' => $publishedCourse1->id,
                'student_id' => $student->id,
            ], [
                'enrolled_at' => now()->subDays(5),
            ]);

            Enrollment::firstOrCreate([
                'course_id' => $archivedCourse->id,
                'student_id' => $student->id,
            ], [
                'enrolled_at' => now()->subMonths(2),
            ]);
        }
    }
}

