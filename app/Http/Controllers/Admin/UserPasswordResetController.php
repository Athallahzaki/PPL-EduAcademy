<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserPasswordResetController extends Controller
{
    /**
     * Handle the password reset request for a user.
     */
    public function __invoke(User $user): RedirectResponse
    {
        Gate::authorize('resetPassword', $user);

        $temporaryPassword = Str::password(16);

        $user->password = Hash::make($temporaryPassword);
        $user->must_change_password = true;
        $user->save();

        // Invalidate all existing sessions for this user
        DB::table('sessions')->where('user_id', $user->id)->delete();

        return back()
            ->with('temporary_password', $temporaryPassword)
            ->with('success', 'User password has been reset successfully. Provide the temporary password to the user.');
    }
}

