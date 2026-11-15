<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the student dashboard request.
     */
    public function __invoke(): View
    {
        $announcements = Announcement::query()
            ->visibleToDashboard()
            ->latest('published_at')
            ->get();

        return view('student.dashboard', compact('announcements'));
    }
}

