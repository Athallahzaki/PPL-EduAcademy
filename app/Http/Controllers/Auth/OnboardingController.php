<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstructorOnboardingRequest;
use App\Http\Requests\StudentOnboardingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding form.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->needsOnboarding()) {
            return redirect()->route($user->dashboardRoute());
        }

        return view('auth.onboarding', compact('user'));
    }

    /**
     * Store the onboarding details.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->needsOnboarding()) {
            return redirect()->route($user->dashboardRoute());
        }

        if ($user->isStudent()) {
            $studentRequest = app(StudentOnboardingRequest::class);

            $user->education_level = $studentRequest->education_level;
            $user->onboarded_at = now();
            $user->save();

            return redirect()->route($user->dashboardRoute())
                ->with('success', 'Onboarding completed successfully.');
        }

        if ($user->isInstructor()) {
            $instructorRequest = app(InstructorOnboardingRequest::class);

            // Store CV on private disk (local disk points to storage/app/private)
            $cvPath = $instructorRequest->file('cv')->store('instructor-cvs', 'local');

            $user->instructorProfile()->create([
                'bio' => $instructorRequest->bio,
                'expertise' => $instructorRequest->expertise,
                'cv_path' => $cvPath,
                'portfolio_url' => $instructorRequest->portfolio_url,
            ]);

            $user->onboarded_at = now();
            $user->save();

            return redirect()->route($user->dashboardRoute())
                ->with('success', 'Onboarding completed successfully.');
        }

        // If admin (or unsupported), mark as onboarded
        $user->onboarded_at = now();
        $user->save();

        return redirect()->route($user->dashboardRoute());
    }
}

