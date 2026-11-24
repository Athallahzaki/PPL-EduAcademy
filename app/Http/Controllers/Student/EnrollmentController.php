<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EnrollmentController extends Controller
{
    /**
     * Handle one-click instant student course enrollment.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('enroll', $course);

        try {
            Enrollment::firstOrCreate([
                'course_id' => $course->id,
                'student_id' => $request->user()->id,
            ], [
                'enrolled_at' => now(),
            ]);
        } catch (UniqueConstraintViolationException) {
            // Idempotent: catch race condition or double submission gracefully without throwing 500 error (§7.3)
        }

        return redirect()->route('student.courses.show', $course)
            ->with('success', "You are now enrolled in {$course->title}.");
    }
}

