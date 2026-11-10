<x-layouts.app title="Student Dashboard">
    <div class="space-y-6">
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-indigo-700 to-indigo-900 rounded-2xl p-6 sm:p-8 text-white shadow-sm">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Welcome, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-indigo-100 max-w-2xl text-sm leading-relaxed">
                Welcome to your EduAcademy Student Learning Dashboard. Access enrolled classes, submit your assignments, prepare for quizzes, and apply for educational scholarships.
            </p>
        </div>

        <!-- Student Modules Placeholder Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-card>
                <div class="flex items-center gap-3 mb-3">
                    <span class="rounded-lg bg-indigo-50 p-2.5 text-indigo-600">
                        <x-icon name="collection" class="h-6 w-6" />
                    </span>
                    <h3 class="text-base font-semibold text-gray-900">Enrolled Courses</h3>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Track your registered courses and continue your learning modules where you left off. Course enrolment arrives in Sprint 2.
                </p>
            </x-card>

            <x-card>
                <div class="flex items-center gap-3 mb-3">
                    <span class="rounded-lg bg-amber-50 p-2.5 text-amber-600">
                        <x-icon name="clipboard-list" class="h-6 w-6" />
                    </span>
                    <h3 class="text-base font-semibold text-gray-900">Assignments & Quizzes</h3>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Check upcoming deadlines, complete homework assignments, and view assessment scores. Arriving in Sprint 2.
                </p>
            </x-card>

            <x-card>
                <div class="flex items-center gap-3 mb-3">
                    <span class="rounded-lg bg-emerald-50 p-2.5 text-emerald-600">
                        <x-icon name="academic-cap" class="h-6 w-6" />
                    </span>
                    <h3 class="text-base font-semibold text-gray-900">Scholarships</h3>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Explore available education aid and scholarship opportunities and track application verification status. Arriving in Sprint 3.
                </p>
            </x-card>
        </div>
    </div>
</x-layouts.app>

