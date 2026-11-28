<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\DiscussionThread;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the student dashboard request.
     */
    public function __invoke(Request $request): View
    {
        $student = $request->user();

        // PBI-08: Announcements banner
        $announcements = Announcement::query()
            ->visibleToDashboard()
            ->latest('published_at')
            ->get();

        // PBI-11: Continue Learning section (up to 3 most recent enrollments) (§9)
        $recentCourses = $student->enrolledCourses()
            ->with(['subject', 'instructor'])
            ->withCount('enrollments')
            ->orderByPivot('enrolled_at', 'desc')
            ->take(3)
            ->get();

        // PBI-14 & PBI-15: Forum Diskusi Terbaru
        $recentThreads = DiscussionThread::query()
            ->with(['user', 'subject'])
            ->withCount('comments')
            ->latest('created_at')
            ->take(3)
            ->get();

        return view('student.dashboard', compact('announcements', 'recentCourses', 'recentThreads'));
    }
}
