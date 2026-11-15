<?php

namespace App\Http\Controllers\Instructor;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CourseStatusController extends Controller
{
    /**
     * Update the course status.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('changeStatus', $course);

        $request->validate([
            'status' => ['required', Rule::enum(CourseStatus::class)],
        ]);

        $newStatus = CourseStatus::from($request->status);
        $enrollmentsCount = $course->enrollments()->count();

        // Enforce state machine rules (§3.5)
        if (! $course->status->canTransitionTo($newStatus, $enrollmentsCount)) {
            if ($course->status === CourseStatus::Published && $newStatus === CourseStatus::Draft && $enrollmentsCount > 0) {
                throw ValidationException::withMessages([
                    'status' => 'Students are already enrolled in this class. It cannot be moved back to draft.',
                ]);
            }

            throw ValidationException::withMessages([
                'status' => "Cannot change course status from {$course->status->label()} to {$newStatus->label()}.",
            ]);
        }

        $course->status = $newStatus;
        $course->save();

        return back()->with('success', "Class status changed to {$newStatus->label()}.");
    }
}

