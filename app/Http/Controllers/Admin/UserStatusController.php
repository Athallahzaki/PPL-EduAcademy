<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class UserStatusController extends Controller
{
    /**
     * Update the user status.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'status' => ['required', new Enum(AccountStatus::class)],
        ]);

        $newStatus = AccountStatus::from($request->status);

        // Check policy
        Gate::authorize('changeStatus', [$user, $newStatus]);

        $user->status = $newStatus;
        $user->save();

        // If suspended, invalidate all active sessions immediately
        if ($newStatus === AccountStatus::Suspended) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return back()->with('success', "Account status has been changed to {$newStatus->label()}.");
    }
}

