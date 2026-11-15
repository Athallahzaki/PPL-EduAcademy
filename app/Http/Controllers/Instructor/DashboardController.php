<?php

namespace App\Http\Controllers\Instructor;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the instructor dashboard request.
     */
    public function __invoke(Request $request): View
    {
        $instructor = $request->user();

        // PBI-08: Announcements banner
        $announcements = Announcement::query()
            ->visibleToDashboard()
            ->latest('published_at')
            ->get();

        // PBI-11: Instructor Course Metrics (§9)
        $publishedCount = Course::ownedBy($instructor)->published()->count();
        $draftCount = Course::ownedBy($instructor)->where('status', CourseStatus::Draft->value)->count();
        $totalStudents = Course::ownedBy($instructor)->withCount('enrollments')->get()->sum('enrollments_count');

        return view('instructor.dashboard', compact(
            'announcements',
            'publishedCount',
            'draftCount',
            'totalStudents'
        ));
    }
}
