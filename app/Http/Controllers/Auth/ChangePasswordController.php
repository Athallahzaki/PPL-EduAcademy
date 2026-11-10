<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ChangePasswordController extends Controller
{
    /**
     * Show the change password form.
     */
    public function show(): View
    {
        return view('auth.change-password');
    }

    /**
     * Update the user password.
     */
    public function update(ChangePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        return redirect()->route($user->dashboardRoute())
            ->with('success', 'Your password has been changed successfully.');
    }
}

