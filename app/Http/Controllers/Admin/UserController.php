<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $query = User::query();

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        if ($request->filled('role')) {
            $query->withRole($request->input('role'));
        }

        if ($request->filled('status')) {
            $query->withStatus($request->input('status'));
        }

        $users = $query->latest('created_at')->paginate(15)->withQueryString();

        // Summary counts
        $pendingInstructorsCount = User::where('role', Role::Instructor->value)
            ->where('status', AccountStatus::Pending->value)
            ->count();
        $activeCount = User::where('status', AccountStatus::Active->value)->count();
        $suspendedCount = User::where('status', AccountStatus::Suspended->value)->count();

        return view('admin.users.index', compact(
            'users',
            'pendingInstructorsCount',
            'activeCount',
            'suspendedCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $role = Role::from($request->role);
        $temporaryPassword = Str::password(16);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($temporaryPassword);
        $user->role = $role;
        $user->status = AccountStatus::Active;
        $user->must_change_password = true;
        $user->onboarded_at = $role === Role::Admin ? now() : null;
        $user->save();

        return redirect()->route('admin.users.show', $user)
            ->with('temporary_password', $temporaryPassword)
            ->with('success', 'User account created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        Gate::authorize('view', $user);

        $user->load('instructorProfile');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $newRole = Role::from($request->role);

        // Guard against unauthorized or invalid role change
        if ($newRole !== $user->role) {
            Gate::authorize('changeRole', [$user, $newRole]);
            $user->role = $newRole;

            // If promoting to admin, set onboarded_at if null
            if ($newRole === Role::Admin && $user->onboarded_at === null) {
                $user->onboarded_at = now();
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        // Clear active sessions
        DB::table('sessions')->where('user_id', $user->id)->delete();

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User account deleted (soft delete) successfully.');
    }

    /**
     * Restore the soft-deleted user.
     */
    public function restore(int|string $id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);

        Gate::authorize('restore', $user);

        $user->restore();

        return redirect()->route('admin.users.index')
            ->with('success', 'User account restored successfully.');
    }
}

