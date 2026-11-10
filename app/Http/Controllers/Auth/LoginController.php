<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function show(): View
    {
        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();

        // 1. Verify credentials first to not disclose account states to invalid password attempts
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        // 2. Account state check
        if ($user->status === AccountStatus::Pending) {
            throw ValidationException::withMessages([
                'email' => 'Your account is awaiting administrator approval.',
            ]);
        }

        if ($user->status === AccountStatus::Rejected) {
            throw ValidationException::withMessages([
                'email' => 'Your registration was not approved. Please contact an administrator.',
            ]);
        }

        if ($user->status === AccountStatus::Suspended) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been suspended. Please contact an administrator.',
            ]);
        }

        if ($user->status !== AccountStatus::Active) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        // 3. Successful login
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Per spec §4.2: ignore url.intended and always redirect to role dashboard
        return redirect()->route($user->dashboardRoute());
    }
}

