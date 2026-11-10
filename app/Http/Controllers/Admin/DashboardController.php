<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
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

        return view('admin.dashboard', compact(
            'totalUsers',
            'pendingInstructors',
            'activeStudents',
            'activeInstructors'
        ));
    }
}

