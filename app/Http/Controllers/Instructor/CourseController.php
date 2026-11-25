<?php

namespace App\Http\Controllers\Instructor;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of instructor courses.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Course::class);

        $instructor = $request->user();

        $baseQuery = Course::ownedBy($instructor);

        // Counts for filter chips
        $totalCount = (clone $baseQuery)->count();
        $draftCount = (clone $baseQuery)->where('status', CourseStatus::Draft->value)->count();
        $publishedCount = (clone $baseQuery)->where('status', CourseStatus::Published->value)->count();
        $archivedCount = (clone $baseQuery)->where('status', CourseStatus::Archived->value)->count();

        $query = (clone $baseQuery)->with(['subject'])->withCount('enrollments');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        $courses = $query->latest('updated_at')->paginate(10)->withQueryString();

        return view('instructor.courses.index', compact(
            'courses',
            'totalCount',
            'draftCount',
            'publishedCount',
            'archivedCount'
        ));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): View
    {
        Gate::authorize('create', Course::class);

        $subjects = Subject::orderBy('name')->get();

        return view('instructor.courses.create', compact('subjects'));
    }

    /**
     * Store a newly created course.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        Gate::authorize('create', Course::class);

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('course-banners', 'public');
        }

        $course = new Course();
        $course->instructor_id = $request->user()->id;
        $course->subject_id = $request->subject_id;
        $course->title = $request->title;
        $course->description = $request->description;
        $course->level = CourseLevel::from($request->level);
        $course->status = CourseStatus::Draft;
        $course->banner_path = $bannerPath;
        $course->save();

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Class created as a draft. Publish it when you are ready.');
    }

    /**
     * Display the course hub overview.
     */
    public function show(Course $course): View
    {
        Gate::authorize('manage', $course);

        $course->load(['subject', 'instructor'])->loadCount('enrollments');

        $modulesCount = $course->modules()->count();
        $publishedModulesCount = $course->modules()->published()->count();
        $chaptersCount = $course->chapters()->count();
        $publishedChaptersCount = $course->chapters()->where('chapters.is_published', true)->count();

        return view('instructor.courses.show', compact(
            'course',
            'modulesCount',
            'publishedModulesCount',
            'chaptersCount',
            'publishedChaptersCount'
        ));
    }

    /**
     * Show the form for editing the course.
     */
    public function edit(Course $course): View
    {
        Gate::authorize('manage', $course);

        $subjects = Subject::orderBy('name')->get();

        return view('instructor.courses.edit', compact('course', 'subjects'));
    }

    /**
     * Update the course.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('manage', $course);

        if ($request->boolean('remove_banner')) {
            if ($course->banner_path) {
                Storage::disk('public')->delete($course->banner_path);
                $course->banner_path = null;
            }
        } elseif ($request->hasFile('banner')) {
            if ($course->banner_path) {
                Storage::disk('public')->delete($course->banner_path);
            }
            $course->banner_path = $request->file('banner')->store('course-banners', 'public');
        }

        $course->subject_id = $request->subject_id;
        $course->title = $request->title;
        $course->description = $request->description;
        $course->level = CourseLevel::from($request->level);
        // Note: status is never modified through the edit form
        $course->save();

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Class details updated successfully.');
    }

    /**
     * Remove the course from storage (soft delete).
     */
    public function destroy(Course $course): RedirectResponse
    {
        Gate::authorize('delete', $course);

        if ($course->banner_path) {
            Storage::disk('public')->delete($course->banner_path);
            $course->banner_path = null;
            $course->save();
        }

        $course->delete();

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Class deleted successfully.');
    }
}

