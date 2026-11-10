<x-layouts.app title="Manage Accounts & Roles">
    <div class="space-y-6">
        <!-- Page Header -->
        <x-page-header title="Manage Accounts & Roles" description="Review all registered user accounts, manage permission roles, and approve new instructor requests.">
            <x-slot:actions>
                <a
                    href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create User
                </a>
            </x-slot:actions>
        </x-page-header>

        <!-- Summary Chip Row (§6.1) -->
        <div class="flex flex-wrap items-center gap-3">
            <a
                href="{{ route('admin.users.index', ['role' => 'instructor', 'status' => 'pending']) }}"
                class="inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-sm ring-1 ring-amber-600' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 ring-1 ring-amber-600/30' }}"
            >
                <span class="h-2 w-2 rounded-full {{ request('status') === 'pending' ? 'bg-white' : 'bg-amber-500' }}"></span>
                Pending Instructors ({{ $pendingInstructorsCount }})
            </a>

            <a
                href="{{ route('admin.users.index', ['status' => 'active']) }}"
                class="inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all {{ request('status') === 'active' && !request('role') ? 'bg-emerald-600 text-white shadow-sm ring-1 ring-emerald-600' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 ring-1 ring-emerald-600/30' }}"
            >
                <span class="h-2 w-2 rounded-full {{ request('status') === 'active' && !request('role') ? 'bg-white' : 'bg-emerald-500' }}"></span>
                Active Accounts ({{ $activeCount }})
            </a>

            <a
                href="{{ route('admin.users.index', ['status' => 'suspended']) }}"
                class="inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all {{ request('status') === 'suspended' ? 'bg-rose-600 text-white shadow-sm ring-1 ring-rose-600' : 'bg-rose-50 text-rose-800 hover:bg-rose-100 ring-1 ring-rose-600/30' }}"
            >
                <span class="h-2 w-2 rounded-full {{ request('status') === 'suspended' ? 'bg-white' : 'bg-rose-500' }}"></span>
                Suspended Accounts ({{ $suspendedCount }})
            </a>

            @if(request()->hasAny(['q', 'role', 'status', 'trashed']))
                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-gray-900 underline ml-2"
                >
                    Reset all filters
                </a>
            @endif
        </div>

        <!-- Filter Form -->
        <x-card class="p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <x-label for="q" value="Search name or email" />
                    <x-input
                        id="q"
                        type="search"
                        name="q"
                        :value="request('q')"
                        placeholder="Search by name or email..."
                    />
                </div>

                <!-- Role Filter -->
                <div>
                    <x-label for="role" value="Role" />
                    <x-select id="role" name="role">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="instructor" {{ request('role') === 'instructor' ? 'selected' : '' }}>Instructor</option>
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                    </x-select>
                </div>

                <!-- Status Filter -->
                <div>
                    <x-label for="status" value="Status" />
                    <x-select id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </x-select>
                </div>

                <!-- Actions & Trashed Toggle -->
                <div class="flex items-center justify-between gap-3 pt-1 sm:pt-0">
                    <label class="inline-flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                        <input
                            type="checkbox"
                            name="trashed"
                            value="1"
                            {{ request('trashed') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <span>Show deleted</span>
                    </label>
                    <x-button type="submit" size="md">
                        Filter
                    </x-button>
                </div>
            </form>
        </x-card>

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
                                    <span class="ml-2 inline-flex items-center rounded-md bg-gray-100 px-1.5 py-0.5 text-[10px] font-medium text-gray-600">Deleted</span>
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
                        <x-badge :variant="$user->role?->value ?? 'gray'">
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
                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                        <div class="flex items-center justify-end gap-2">
                            @if ($user->trashed())
                                <!-- Restore Action -->
                                <form method="POST" action="{{ route('admin.users.restore', $user->id) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-emerald-50 px-2.5 py-1 text-emerald-700 hover:bg-emerald-100 font-semibold transition-colors"
                                    >
                                        Restore
                                    </button>
                                </form>
                            @else
                                <!-- Status Actions -->
                                @if ($user->status === \App\Enums\AccountStatus::Pending)
                                    <!-- Approve -->
                                    <form method="POST" action="{{ route('admin.users.status', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="active">
                                        <button
                                            type="submit"
                                            class="rounded-lg bg-emerald-600 px-2.5 py-1 text-white hover:bg-emerald-700 font-semibold transition-colors shadow-xs"
                                        >
                                            Approve
                                        </button>
                                    </form>

                                    <!-- Reject -->
                                    <form method="POST" action="{{ route('admin.users.status', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button
                                            type="submit"
                                            class="rounded-lg bg-gray-100 px-2.5 py-1 text-gray-700 hover:bg-gray-200 font-semibold transition-colors"
                                        >
                                            Reject
                                        </button>
                                    </form>
                                @elseif ($user->status === \App\Enums\AccountStatus::Active && auth()->id() !== $user->id)
                                    <!-- Suspend -->
                                    <form method="POST" action="{{ route('admin.users.status', $user) }}" onsubmit="return confirm('Are you sure you want to suspend this account? The user will be immediately logged out.');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="suspended">
                                        <button
                                            type="submit"
                                            class="rounded-lg bg-rose-50 px-2.5 py-1 text-rose-700 hover:bg-rose-100 font-semibold transition-colors"
                                        >
                                            Suspend
                                        </button>
                                    </form>
                                @elseif (in_array($user->status, [\App\Enums\AccountStatus::Suspended, \App\Enums\AccountStatus::Rejected]))
                                    <!-- Reactivate -->
                                    <form method="POST" action="{{ route('admin.users.status', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="active">
                                        <button
                                            type="submit"
                                            class="rounded-lg bg-emerald-50 px-2.5 py-1 text-emerald-700 hover:bg-emerald-100 font-semibold transition-colors"
                                        >
                                            Reactivate
                                        </button>
                                    </form>
                                @endif

                                <!-- Details link -->
                                <a
                                    href="{{ route('admin.users.show', $user) }}"
                                    class="text-indigo-600 hover:text-indigo-900 font-semibold px-1"
                                >
                                    Details
                                </a>

                                <!-- Edit link -->
                                <a
                                    href="{{ route('admin.users.edit', $user) }}"
                                    class="text-gray-600 hover:text-gray-900 font-semibold px-1"
                                >
                                    Edit
                                </a>

                                <!-- Delete button (guarded against own account & last active admin) -->
                                @if (auth()->id() !== $user->id && !($user->isAdmin() && $user->isActive() && \App\Models\User::activeAdminCount() <= 1))
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to soft-delete this user? All their active sessions will be terminated.');">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="text-rose-600 hover:text-rose-900 font-semibold px-1"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </td>
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

