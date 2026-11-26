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

        // Demo Curriculum Modules & Chapters for publishedCourse1 (Sprint 3: PBI-12 §9)
        // Module 1: Published
        $module1 = \App\Models\Module::firstOrCreate(
            [
                'course_id' => $publishedCourse1->id,
                'title' => 'Module 1: Foundations & Architecture',
            ],
            [
                'description' => 'Core architectural concepts and fundamental building blocks of modern web applications.',
                'position' => 1,
                'is_published' => true,
            ]
        );

        // Module 1 - Chapter 1: Published Text Chapter (Rich Markdown)
        \App\Models\Chapter::firstOrCreate(
            [
                'module_id' => $module1->id,
                'title' => 'Course Overview & Prerequisites',
            ],
            [
                'type' => \App\Enums\ChapterType::Text,
                'body' => "# Welcome to Modern Full-Stack Development\n\nIn this foundational lesson, we explore the essential developer workflow, framework architecture, and environment dependencies.\n\n### Required Tools\n- PHP 8.3 or higher\n- Composer 2.7+\n- Node.js LTS (v20+)\n- Docker Desktop\n\n### Verifying Your Setup\nRun the following shell command to confirm your PHP version:\n\n```bash\nphp -v\ncomposer --version\n```\n\n> **Important:** Keep your database migrations in sync before beginning module exercises.\n\nFor additional official documentation, visit [Laravel Documentation](https://laravel.com).",
                'video_url' => null,
                'position' => 1,
                'is_published' => true,
            ]
        );

        // Module 1 - Chapter 2: Published Video Chapter (YouTube)
        \App\Models\Chapter::firstOrCreate(
            [
                'module_id' => $module1->id,
                'title' => 'Development Environment Walkthrough',
            ],
            [
                'type' => \App\Enums\ChapterType::Video,
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'body' => "Follow along with this video lecture to configure your local IDE, extensions, and automated linters.\n\nKey timestamps:\n- 00:00 Introduction\n- 01:30 Composer & NPM packages\n- 03:00 First migration",
                'position' => 2,
                'is_published' => true,
            ]
        );

        // Module 1 - Chapter 3: Draft Chapter
        \App\Models\Chapter::firstOrCreate(
            [
                'module_id' => $module1->id,
                'title' => 'Advanced Architectural Patterns (Upcoming)',
            ],
            [
                'type' => \App\Enums\ChapterType::Text,
                'body' => "# Advanced Patterns\n\nThis lesson is currently under review by the instructor.",
                'video_url' => null,
                'position' => 3,
                'is_published' => false,
            ]
        );

        // Module 2: Draft Module
        $module2 = \App\Models\Module::firstOrCreate(
            [
                'course_id' => $publishedCourse1->id,
                'title' => 'Module 2: Reactive Frontend with Vue 3 (Draft)',
            ],
            [
                'description' => 'Draft curriculum module demonstrating that published chapters in a draft module remain hidden from students.',
                'position' => 2,
                'is_published' => false,
            ]
        );

        // Module 2 - Chapter 1: Published chapter in draft module
        \App\Models\Chapter::firstOrCreate(
            [
                'module_id' => $module2->id,
                'title' => 'Vue 3 Composition API Essentials',
            ],
            [
                'type' => \App\Enums\ChapterType::Text,
                'body' => "# Composition API\n\nExploration of `ref`, `reactive`, and script setup syntax.",
                'video_url' => null,
                'position' => 1,
                'is_published' => true,
            ]
        );
    }
}

