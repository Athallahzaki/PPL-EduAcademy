<x-layouts.app title="Instructor Dashboard">
    <div class="space-y-6">
        <!-- PBI-08: Announcements Banner -->
        <x-announcement-banner :announcements="$announcements" />

        <!-- Welcome Hero -->
        <div class="bg-gradient-to-r from-indigo-700 to-indigo-900 rounded-2xl p-6 sm:p-8 text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Welcome back, {{ auth()->user()->name }}!</h1>
                <p class="mt-2 text-indigo-100 max-w-xl text-sm leading-relaxed">
                    Your central command for teaching at EduAcademy. Manage your course curriculum, oversee student enrollments, and draft new classes.
                </p>
            </div>

            <div class="shrink-0 flex items-center gap-3">
                <a
                    href="{{ route('instructor.courses.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-indigo-700 shadow-sm hover:bg-indigo-50 transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Class
                </a>
                <a
                    href="{{ route('instructor.profile.show') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 px-4 py-2.5 text-sm font-semibold text-white border border-indigo-300/40 transition-colors"
                >
                    My Profile
                </a>
                <a
                    href="{{ route('instructor.courses.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600/80 hover:bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white border border-indigo-400/40 transition-colors"
                >
                    My Classes
                </a>
            </div>
        </div>

        <!-- Instructor Metric Stat Cards (§9) -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <x-stat-card
                title="Published Classes"
                :value="$publishedCount"
                icon="book-open"
                variant="emerald"
                description="Live in student catalog"
                :link="route('instructor.courses.index', ['status' => 'published'])"
            />

            <x-stat-card
                title="Draft Classes"
                :value="$draftCount"
                icon="document-text"
                variant="amber"
                description="In preparation / unpublished"
                :link="route('instructor.courses.index', ['status' => 'draft'])"
            />

            <x-stat-card
                title="Total Students Enrolled"
                :value="$totalStudents"
                icon="users"
                variant="indigo"
                description="Across all your classes"
                :link="route('instructor.courses.index')"
            />
        </div>

        <!-- Quick Access Section -->
        <x-card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">Course Management Hub</h3>
                    <a href="{{ route('instructor.courses.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                        View all classes &rarr;
                    </a>
                </div>
            </x-slot:header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-600">
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                    <h4 class="font-bold text-gray-900 text-sm mb-1">Class Publishing Lifecycle</h4>
                    <p class="leading-relaxed text-gray-500">
                        New classes are created in <strong>Draft</strong> mode so you can prepare materials. When you're ready, publish the class to make it visible in the student catalog.
                    </p>
                </div>

                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                    <h4 class="font-bold text-gray-900 text-sm mb-1">Upcoming in Sprint 3</h4>
                    <p class="leading-relaxed text-gray-500">
                        Lesson modules, assignments grading, quizzes, and live interactive lectures will integrate directly into your Class Hub.
                    </p>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.app>
