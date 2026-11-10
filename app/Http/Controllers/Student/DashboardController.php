<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the student dashboard request.
     */
    public function __invoke(): View
    {
        return view('student.dashboard');
    }
}

