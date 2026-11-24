<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determine whether the user can view any courses.
     */
    public function viewAny(User $user): bool
    {
        return $user->isInstructor();
    }

    /**
     * Determine whether the user can create courses.
     */
    public function create(User $user): bool
    {
        return $user->isInstructor();
    }

    /**
     * General management check for instructor hub features.
     * Used by PBI-11 and all Sprint 3 hub tabs (PBI-12, 13, 05, 06, 09).
     */
    public function manage(User $user, Course $course): bool
    {
        return $user->isInstructor() && $course->instructor_id === $user->id;
    }

    /**
     * Determine whether the instructor can view the class hub.
     */
    public function view(User $user, Course $course): bool
    {
        return $this->manage($user, $course);
    }

    /**
     * Determine whether the instructor can edit/update the course.
     */
    public function update(User $user, Course $course): bool
    {
        return $this->manage($user, $course);
    }

    /**
     * Determine whether the instructor can alter the status of the course.
     */
    public function changeStatus(User $user, Course $course): bool
    {
        return $this->manage($user, $course);
    }

    /**
     * Determine whether the instructor can delete the course.
     * Permitted only when the class has zero enrolled students.
     */
    public function delete(User $user, Course $course): Response
    {
        if (! $this->manage($user, $course)) {
            return Response::deny();
        }

        if ($course->enrollments()->count() > 0) {
            return Response::deny('Classes with enrolled students cannot be deleted. Archive it instead.');
        }

        return Response::allow();
    }

    /**
     * Determine whether a student can browse/view the course in the catalog.
     * Must be enrollable, OR the student is already enrolled.
     * Otherwise denied as a 404 to avoid leaking draft/hidden courses.
     */
    public function browse(User $user, Course $course): Response
    {
        if (! $user->isStudent()) {
            return Response::denyAsNotFound();
        }

        if ($course->isEnrollable() || $user->isEnrolledIn($course)) {
            return Response::allow();
        }

        return Response::denyAsNotFound();
    }

    /**
     * Determine whether a student can enroll in the course.
     */
    public function enroll(User $user, Course $course): Response
    {
        if (! $user->isStudent()) {
            return Response::deny('Only students can enroll in courses.');
        }

        if (! $course->isEnrollable()) {
            return Response::denyAsNotFound();
        }

        if ($user->isEnrolledIn($course)) {
            return Response::deny('You are already enrolled in this class.');
        }

        return Response::allow();
    }

    /**
     * Determine whether a student can access the course learning area.
     * Used by PBI-11 (course home) and Sprint 3 (PBI-12, 13, 05, 06, 09).
     */
    public function access(User $user, Course $course): bool
    {
        return $user->isStudent() && $user->isEnrolledIn($course);
    }
}

