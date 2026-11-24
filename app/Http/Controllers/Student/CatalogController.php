<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Display a listing of public/enrollable courses in the catalog.
     */
    public function index(Request $request): View
    {
        $subjects = Subject::orderBy('name')->get();

        $query = Course::visibleInCatalog()
            ->with(['subject', 'instructor.instructorProfile'])
            ->withCount('enrollments');

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        if ($request->filled('subject')) {
            $query->where('subject_id', $request->input('subject'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        $courses = $query->latest('created_at')->paginate(9)->withQueryString();

        // Pluck enrolled course IDs in one single query to mark cards without N+1 queries (§7.1)
        $enrolledCourseIds = $request->user()->enrolledCourses()->pluck('courses.id')->toArray();

        return view('student.catalog.index', compact('courses', 'subjects', 'enrolledCourseIds'));
    }

    /**
     * Display the specified course detail in the catalog.
     */
    public function show(Request $request, Course $course): View|RedirectResponse
    {
        Gate::authorize('browse', $course);

        // If the student is already enrolled, redirect directly to the class learning hub (§5 / §7.2)
        if ($request->user()->isEnrolledIn($course)) {
            return redirect()->route('student.courses.show', $course);
        }

        $course->load(['subject', 'instructor.instructorProfile'])->loadCount('enrollments');

        return view('student.catalog.show', compact('course'));
    }
}

