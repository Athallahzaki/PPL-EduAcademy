<x-layouts.app title="Manage Accounts & Roles">
    <div class="space-y-6">
        <!-- Page Header -->
        <x-page-header title="Manage Accounts & Roles" description="Review all registered user accounts, manage permission roles, and approve new instructor requests.">
            <x-slot:actions>
                <x-button :href="route('admin.users.create')">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create User
                </x-button>
            </x-slot:actions>
        </x-page-header>

        <!-- Status Chips -->
        <div class="flex flex-wrap items-center gap-2.5">
            <x-filter-chip
                :href="route('admin.users.index', ['role' => 'instructor', 'status' => 'pending'])"
                tone="amber"
                dot
                :active="request('status') === 'pending'"
                :count="$pendingInstructorsCount"
            >Pending Instructors</x-filter-chip>

            <x-filter-chip
                :href="route('admin.users.index', ['status' => 'active'])"
                tone="emerald"
                dot
                :active="request('status') === 'active' && ! request('role')"
                :count="$activeCount"
            >Active Accounts</x-filter-chip>

            <x-filter-chip
                :href="route('admin.users.index', ['status' => 'suspended'])"
                tone="rose"
                dot
                :active="request('status') === 'suspended'"
                :count="$suspendedCount"
            >Suspended Accounts</x-filter-chip>
        </div>

        <!-- Filter Bar -->
        <x-filter-bar
            :action="route('admin.users.index')"
            :active="request()->hasAny(['q', 'role', 'status', 'trashed'])"
            cols="lg:grid-cols-5"
        >
            <x-search-field class="lg:col-span-2" label="Search" placeholder="Search by name or email..." />

            <div>
                <x-label for="role" value="Role" />
                <x-select id="role" name="role">
                    <option value="">All Roles</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                    <option value="instructor" @selected(request('role') === 'instructor')>Instructor</option>
                    <option value="student" @selected(request('role') === 'student')>Student</option>
                </x-select>
            </div>

            <div>
                <x-label for="status" value="Status" />
                <x-select id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </x-select>
            </div>

            <x-filter-toggle name="trashed" label="Show deleted" :checked="(bool) request('trashed')" />
        </x-filter-bar>

        <!-- Users Table -->
        <x-table>
            <x-slot:headers>
                <th scope="col" class="px-6 py-3.5">User</th>
                <th scope="col" class="px-6 py-3.5">Email</th>
                <th scope="col" class="px-6 py-3.5">Role</th>
                <th scope="col" class="px-6 py-3.5">Status</th>
                <th scope="col" class="px-6 py-3.5 text-center">Onboarded</th>
                <th scope="col" class="px-6 py-3.5">Registered</th>
                <th scope="col" class="px-6 py-3.5 text-right">Actions</th>
            </x-slot:headers>

            @forelse ($users as $user)
                <tr class="hover:bg-gray-50/75 transition-colors {{ $user->trashed() ? 'bg-gray-50/50 opacity-75' : '' }}">
                    <!-- Name -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-700 font-bold text-xs uppercase">
                                {{ substr($user->name, 0, 1) }}
                            </span>
                            <div>
                                <x-user-name :user="$user" />
                                @if($user->trashed())
                                    <x-badge variant="red" size="sm" class="ml-2">Deleted</x-badge>
                                @endif
                            </div>
                        </div>
                    </td>

                    <!-- Email -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono text-xs">
                        {{ $user->email }}
                    </td>

                    <!-- Role Badge -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge :variant="$user->role?->badgeColor() ?? 'gray'">
                            {{ $user->role?->label() ?? ucfirst($user->role ?? '') }}
                        </x-badge>
                    </td>

                    <!-- Status Badge -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge :variant="$user->status?->badgeColor() ?? 'gray'">
                            {{ $user->status?->label() ?? ucfirst($user->status ?? '') }}
                        </x-badge>
                    </td>

                    <!-- Onboarded State -->
                    <td class="px-6 py-4 whitespace-nowrap text-center text-xs">
                        @if ($user->onboarded_at)
                            <span class="text-emerald-600 font-bold" title="Onboarded at {{ $user->onboarded_at->format('Y-m-d H:i') }}">✓</span>
                        @else
                            <span class="text-gray-400 font-bold">—</span>
                        @endif
                    </td>

                    <!-- Registered At -->
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>

                    <!-- Row Actions -->
                    <x-table.actions>
                        @if ($user->trashed())
                            <x-row-action :action="route('admin.users.restore', $user->id)" variant="success">Restore</x-row-action>
                        @else
                            @if ($user->status === \App\Enums\AccountStatus::Pending)
                                <x-row-action :action="route('admin.users.status', $user)" method="PATCH" :fields="['status' => 'active']" variant="success">Approve</x-row-action>
                                <x-row-action :action="route('admin.users.status', $user)" method="PATCH" :fields="['status' => 'rejected']" variant="muted">Reject</x-row-action>
                            @elseif ($user->status === \App\Enums\AccountStatus::Active && auth()->id() !== $user->id)
                                <x-row-action
                                    :action="route('admin.users.status', $user)"
                                    method="PATCH"
                                    :fields="['status' => 'suspended']"
                                    variant="danger"
                                    confirm="Are you sure you want to suspend this account? The user will be immediately logged out."
                                >Suspend</x-row-action>
                            @elseif (in_array($user->status, [\App\Enums\AccountStatus::Suspended, \App\Enums\AccountStatus::Rejected]))
                                <x-row-action :action="route('admin.users.status', $user)" method="PATCH" :fields="['status' => 'active']" variant="success">Reactivate</x-row-action>
                            @endif

                            <x-row-action :href="route('admin.users.show', $user)" variant="primary">View</x-row-action>
                            <x-row-action :href="route('admin.users.edit', $user)">Edit</x-row-action>

                            @if (auth()->id() !== $user->id && ! ($user->isAdmin() && $user->isActive() && \App\Models\User::activeAdminCount() <= 1))
                                <x-row-action
                                    :action="route('admin.users.destroy', $user)"
                                    method="DELETE"
                                    variant="danger"
                                    confirm="Are you sure you want to delete this user? All their active sessions will be terminated."
                                >Delete</x-row-action>
                            @endif
                        @endif
                    </x-table.actions>
                </tr>
            @empty
                <x-table.empty colspan="7" message="No user accounts match your search or filters." />
            @endforelse
        </x-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.app>

