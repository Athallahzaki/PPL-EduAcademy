<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the instructor dashboard request.
     */
    public function __invoke(): View
    {
        return view('instructor.dashboard');
    }
}

