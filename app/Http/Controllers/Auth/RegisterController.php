<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function show(): View
    {
        return view('auth.register');
    }

    /**
     * Handle the registration request.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $role = $request->input('register_as') === 'instructor' ? Role::Instructor : Role::Student;

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $role;
        $user->must_change_password = false;
        $user->onboarded_at = null;

        if ($role === Role::Student) {
            $user->status = AccountStatus::Active;
            $user->save();

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('onboarding.show');
        }

        // Instructor registration
        $user->status = AccountStatus::Pending;
        $user->save();

        return redirect()->route('login')->with(
            'status',
            'Registration received. An administrator must approve your account before you can sign in.'
        );
    }
}

