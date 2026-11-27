<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseReviewRequest;
use App\Http\Requests\UpdateCourseReviewRequest;
use App\Models\Course;
use App\Models\CourseReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CourseReviewController extends Controller
{
    public function store(StoreCourseReviewRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('create', [CourseReview::class, $course]);

        $course->reviews()->create([
            'student_id' => $request->user()->id,
            'rating' => $request->validated('rating'),
            'review' => $request->validated('review'),
        ]);

        return redirect()->route('student.courses.show', $course)
            ->with('success', 'Ulasan berhasil ditambahkan.');
    }

    public function update(UpdateCourseReviewRequest $request, CourseReview $review): RedirectResponse
    {
        Gate::authorize('update', $review);

        $review->update($request->safe()->only(['rating', 'review']));

        return redirect()->route('student.courses.show', $review->course_id)
            ->with('success', 'Ulasan berhasil diperbarui.');
    }

    public function destroy(CourseReview $review): RedirectResponse
    {
        Gate::authorize('delete', $review);

        $courseId = $review->course_id;
        $review->delete();

        return redirect()->route('student.courses.show', $courseId)
            ->with('success', 'Ulasan berhasil dihapus.');
    }

    public function instructorIndex(Course $course): View
    {
        Gate::authorize('manage', $course);

        $averageRating = (float) ($course->reviews()->avg('rating') ?? 0);
        $reviews = $course->reviews()
            ->with('student')
            ->latest()
            ->paginate(10);

        return view('instructor.courses.reviews', compact('course', 'averageRating', 'reviews'));
    }

    public function adminIndex(): View
    {
        $reviews = CourseReview::query()
            ->with(['course', 'student'])
            ->latest()
            ->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function adminDestroy(CourseReview $review): RedirectResponse
    {
        Gate::authorize('delete', $review);

        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Ulasan berhasil dihapus.');
    }
}
