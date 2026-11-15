<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MyCourseController extends Controller
{
    /**
     * Display the student's enrolled courses.
     */
    public function index(Request $request): View
    {
        $courses = $request->user()->enrolledCourses()
            ->with(['subject', 'instructor'])
            ->withCount('enrollments')
            ->orderByPivot('enrolled_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('student.courses.index', compact('courses'));
    }

    /**
     * Display the student course learning hub (home).
     */
    public function show(Request $request, Course $course): View|RedirectResponse
    {
        // If not enrolled, redirect to catalog detail page (or 404 if not browseable) (§5 / §7.5)
        if (! $request->user()->isEnrolledIn($course)) {
            return redirect()->route('student.catalog.show', $course);
        }

        Gate::authorize('access', $course);

        $course->load(['subject', 'instructor.instructorProfile'])->loadCount('enrollments');

        return view('student.courses.show', compact('course'));
    }
}

