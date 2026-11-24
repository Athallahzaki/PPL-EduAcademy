<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\CourseStatus;
use App\Enums\Role;
use App\Enums\ScholarshipStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the admin dashboard request.
     */
    public function __invoke(): View
    {
        $totalUsers = User::count();
        $pendingInstructors = User::where('role', Role::Instructor->value)
            ->where('status', AccountStatus::Pending->value)
            ->count();
        $activeStudents = User::where('role', Role::Student->value)
            ->where('status', AccountStatus::Active->value)
            ->count();
        $activeInstructors = User::where('role', Role::Instructor->value)
            ->where('status', AccountStatus::Active->value)
            ->count();
        $publishedCourses = Course::where('status', CourseStatus::Published->value)->count();
        $totalScholarships = Scholarship::count();
        $publishedScholarships = Scholarship::where('status', ScholarshipStatus::Published->value)->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'pendingInstructors',
            'activeStudents',
            'activeInstructors',
            'publishedCourses',
            'totalScholarships',
            'publishedScholarships'
        ));
    }
}
