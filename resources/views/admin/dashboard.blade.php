<x-layouts.app title="Admin Dashboard">
    <div class="space-y-6">
        <!-- Welcome Hero -->
        <div class="bg-gradient-to-r from-indigo-700 to-indigo-900 rounded-2xl p-6 sm:p-8 text-white shadow-sm">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-indigo-100 max-w-2xl text-sm leading-relaxed">
                EduAcademy administrative control center. Manage users, oversee platform accounts, monitor role lifecycles, and configure academy services.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-indigo-700 shadow-sm hover:bg-indigo-50 transition-colors"
                >
                    <x-icon name="users" class="h-4 w-4 text-indigo-600" />
                    Manage Accounts & Roles
                </a>
                <a
                    href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600/80 hover:bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white border border-indigo-400/40 transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New User Account
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Users -->
            <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Users</p>
                    <span class="rounded-lg bg-indigo-50 p-2 text-indigo-600">
                        <x-icon name="users" class="h-5 w-5" />
                    </span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">{{ $totalUsers }}</p>
                <div class="mt-2 text-xs text-gray-500">All registered system accounts</div>
            </div>

            <!-- Pending Instructors -->
            <div class="bg-white rounded-xl border {{ $pendingInstructors > 0 ? 'border-amber-300 ring-1 ring-amber-300' : 'border-gray-200/80' }} p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold {{ $pendingInstructors > 0 ? 'text-amber-700' : 'text-gray-500' }} uppercase tracking-wider">
                        Pending Approvals
                    </p>
                    <span class="rounded-lg bg-amber-50 p-2 text-amber-600">
                        <x-icon name="clipboard-check" class="h-5 w-5" />
                    </span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight {{ $pendingInstructors > 0 ? 'text-amber-600' : 'text-gray-900' }}">
                    {{ $pendingInstructors }}
                </p>
                <div class="mt-2 text-xs">
                    @if ($pendingInstructors > 0)
                        <a href="{{ route('admin.users.index', ['role' => 'instructor', 'status' => 'pending']) }}" class="font-medium text-amber-700 hover:underline">
                            Review pending instructors &rarr;
                        </a>
                    @else
                        <span class="text-gray-500">No instructors awaiting approval</span>
                    @endif
                </div>
            </div>

            <!-- Active Students -->
            <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Students</p>
                    <span class="rounded-lg bg-emerald-50 p-2 text-emerald-600">
                        <x-icon name="academic-cap" class="h-5 w-5" />
                    </span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">{{ $activeStudents }}</p>
                <div class="mt-2 text-xs text-gray-500">Students enrolled & verified</div>
            </div>

            <!-- Active Instructors -->
            <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Instructors</p>
                    <span class="rounded-lg bg-sky-50 p-2 text-sky-600">
                        <x-icon name="book-open" class="h-5 w-5" />
                    </span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">{{ $activeInstructors }}</p>
                <div class="mt-2 text-xs text-gray-500">Teaching faculty members</div>
            </div>
        </div>

        <!-- Overview Content Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-gray-900">Sprint 1 Foundation Status</h3>
                </x-slot:header>
                <div class="space-y-3 text-sm text-gray-600">
                    <div class="flex items-center justify-between py-1 border-b border-gray-100">
                        <span>Authentication & Sessions</span>
                        <x-badge variant="active">Active (Database Sessions)</x-badge>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-gray-100">
                        <span>Role-Based Access Control</span>
                        <x-badge variant="active">Admin / Instructor / Student</x-badge>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-gray-100">
                        <span>Instructor Onboarding & Approvals</span>
                        <x-badge variant="active">Operational</x-badge>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span>Security & Forced Password Change</span>
                        <x-badge variant="active">Enforced</x-badge>
                    </div>
                </div>
            </x-card>

            <x-card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-gray-900">Upcoming Features (Sprint 2 - 4)</h3>
                </x-slot:header>
                <div class="space-y-2 text-xs text-gray-500">
                    <p>• <strong>PBI-01:</strong> Class Catalog & Course Enrolment</p>
                    <p>• <strong>PBI-02:</strong> Module Content & Chapter Video Player</p>
                    <p>• <strong>PBI-03:</strong> Assignment Submissions & Grading</p>
                    <p>• <strong>PBI-05:</strong> Scholarships Announcement & Verification</p>
                    <p>• <strong>PBI-08:</strong> Live Interactive Sessions & Discussions</p>
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>

