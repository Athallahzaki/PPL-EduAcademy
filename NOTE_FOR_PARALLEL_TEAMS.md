# ⚠️ ARCHITECTURAL & INTEGRATION NOTE FOR PARALLEL TEAMS (SPRINT 2 & 3)

> **IMPORTANT NOTICE FOR DEVELOPERS / AGENTS:**  
> Please read the integration considerations below carefully before implementing your assigned PBI.  
> **ACTION REQUIRED:** Once you have reviewed and integrated these requirements into your PBI implementation, **REMOVE THE LIST** associated with your implementation and commit the remaining considerations, once there are no list left in this file, **DELETE THIS FILE** before committing your changes.

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

