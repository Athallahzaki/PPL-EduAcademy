<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CourseStudentController extends Controller
{
    /**
     * Display the enrolled students for the course.
     */
    public function index(Request $request, Course $course): View
    {
        Gate::authorize('manage', $course);

        $course->load(['subject', 'instructor'])->loadCount('enrollments');

        $query = $course->students();

        if ($request->filled('q')) {
            $term = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($request->input('q')));
            $query->where(function ($sub) use ($term) {
                $sub->where('users.name', 'like', "%{$term}%")
                    ->orWhere('users.email', 'like', "%{$term}%");
            });
        }

        $students = $query->orderByPivot('enrolled_at', 'desc')->paginate(15)->withQueryString();

        return view('instructor.courses.students', compact('course', 'students'));
    }
}

