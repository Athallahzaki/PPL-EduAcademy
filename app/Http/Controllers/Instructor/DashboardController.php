<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the instructor dashboard request.
     */
    public function __invoke(): View
    {
        $announcements = Announcement::query()
            ->visibleToDashboard()
            ->latest('published_at')
            ->get();

        return view('instructor.dashboard', compact('announcements'));
    }
}

