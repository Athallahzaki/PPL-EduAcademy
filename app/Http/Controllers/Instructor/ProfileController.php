<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $profile = $request->user()->instructorProfile;

        return view('instructor.profile.show', compact('profile'));
    }

    public function edit(Request $request): View
    {
        $profile = $request->user()->instructorProfile;

        return view('instructor.profile.edit', compact('profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bio' => ['required', 'string', 'max:1000'],
            'expertise' => ['required', 'string', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'cv' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
        ]);

        $user = $request->user();
        $profile = $user->instructorProfile;

        if ($request->hasFile('cv')) {
            if ($profile?->cv_path && Storage::disk('local')->exists($profile->cv_path)) {
                Storage::disk('local')->delete($profile->cv_path);
            }

            $validated['cv_path'] = $request->file('cv')->store('instructor-cvs', 'local');
        }

        unset($validated['cv']);

        $user->instructorProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('instructor.profile.show')
            ->with('success', 'Profil instruktur berhasil diperbarui.');
    }

    public function downloadCv(Request $request)
    {
        $profile = $request->user()->instructorProfile;

        abort_unless(
            $profile?->cv_path && Storage::disk('local')->exists($profile->cv_path),
            404
        );

        return Storage::disk('local')->download($profile->cv_path, 'CV-'.str($request->user()->name)->slug().'.pdf');
    }
}
