# ⚠️ ARCHITECTURAL & INTEGRATION NOTE FOR PARALLEL TEAMS (SPRINT 2 & 3)

> **IMPORTANT NOTICE FOR DEVELOPERS / AGENTS:**  
> Please read the integration considerations below carefully before implementing your assigned PBI.  
> **ACTION REQUIRED:** Once you have reviewed and integrated these requirements into your PBI implementation, **DELETE THIS FILE** before committing your changes.

---

## 1. Notice for PBI-14: Discussion Forum (Sprint 2)
- **Shared `subjects` Table**:
  - The `subjects` table has already been established by PBI-11 with columns: `id`, `name` (string 80), `slug` (string 80), and timestamps.
  - `SubjectSeeder` is executed automatically in all environments (populating: `Programming`, `Design`, `Mathematics`, `Science`, `Languages`, `Business`, `Other`).
  - **Requirement**: Use `subject_id` as a foreign key constrained to the `subjects` table in your forum migrations/models instead of using a free-text category column.
  - Subject model: `App\Models\Subject`.

---

## 2. Notice for Sprint 3 Teams (PBI-12 Materials, PBI-13 Assignments, PBI-05 Live Sessions, PBI-06 Quizzes, PBI-09 Reviews)
- **Class Hub Architecture (`config/course_tabs.php`)**:
  - The Course Hub operates on a config-driven tab layout. In `config/course_tabs.php`, reserved routes for your features are already predefined:
    - Instructor tabs: `instructor.courses.modules.*`, `instructor.courses.assignments.*`, `instructor.courses.sessions.*`, `instructor.courses.quizzes.*`, `instructor.courses.reviews.*`.
    - Student tabs: `student.courses.modules.*`, `student.courses.assignments.*`, `student.courses.sessions.*`, `student.courses.quizzes.*`, `student.courses.reviews.*`.
  - **Tab Activation**: Tabs automatically appear in the UI as soon as the corresponding named route exists in `routes/web.php` (`Route::has`).
  - **Shared Hub Layout Components**:
    - Wrap instructor pages in:  
      `<x-instructor.course-layout :course="$course" title="Your Page Title"> ... </x-instructor.course-layout>`
    - Wrap student pages in:  
      `<x-student.course-layout :course="$course" title="Your Page Title"> ... </x-student.course-layout>`
    - Do not duplicate header or tab bar markup.
- **Authorization Contracts**:
  - Instructor routes MUST be nested under `/instructor/courses/{course}/...` and call:  
    `Gate::authorize('manage', $course);`
  - Student routes MUST be nested under `/student/courses/{course}/...` and call:  
    `Gate::authorize('access', $course);` (which verifies that the student is actively enrolled in the course).
- **Enrollment Table (`enrollments`)**:
  - Contains `course_id`, `student_id`, `enrolled_at`, and `completed_at` (nullable, available for completion tracking).
  - Model: `App\Models\Enrollment`. Relation on User: `$user->enrolledCourses()`, `$user->isEnrolledIn($course)`.

---
*Remember to delete this file before committing your final PR changes.*

