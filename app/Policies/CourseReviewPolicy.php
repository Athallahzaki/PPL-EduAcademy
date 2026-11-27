<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\CourseReview;
use App\Models\User;

class CourseReviewPolicy
{
    public function create(User $user, Course $course): bool
    {
        return $user->isStudent()
            && $user->isEnrolledIn($course)
            && ! $course->reviews()->where('student_id', $user->id)->exists();
    }

    public function update(User $user, CourseReview $review): bool
    {
        return $user->isStudent() && $review->student_id === $user->id;
    }

    public function delete(User $user, CourseReview $review): bool
    {
        return $user->isAdmin()
            || ($user->isStudent() && $review->student_id === $user->id);
    }
}
