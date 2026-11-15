<x-layouts.app title="Student Dashboard">
    <div class="space-y-6">
        <!-- PBI-08: Announcements Banner -->
        <x-announcement-banner :announcements="$announcements" />

        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-indigo-700 to-indigo-900 rounded-2xl p-6 sm:p-8 text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Welcome, {{ auth()->user()->name }}!</h1>
                <p class="mt-2 text-indigo-100 max-w-xl text-sm leading-relaxed">
                    Track your learning progress, access your enrolled classes, and explore new educational courses in the catalog.
                </p>
            </div>

            <div class="shrink-0 flex items-center gap-3">
                <a
                    href="{{ route('student.catalog.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-indigo-700 shadow-sm hover:bg-indigo-50 transition-colors"
                >
                    <x-icon name="book-open" class="h-4 w-4 text-indigo-600" />
                    Browse Catalog
                </a>
                <a
                    href="{{ route('student.courses.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600/80 hover:bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white border border-indigo-400/40 transition-colors"
                >
                    My Classes
                </a>
            </div>
        </div>

        <!-- Continue Learning Section (§9) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Continue Learning</h2>
                    <p class="text-xs text-gray-500">Pick up right where you left off in your enrolled courses.</p>
                </div>

                @if($recentCourses->isNotEmpty())
                    <a href="{{ route('student.courses.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                        View all classes ({{ auth()->user()->enrolledCourses()->count() }}) &rarr;
                    </a>
                @endif
            </div>

            @if($recentCourses->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($recentCourses as $course)
                        <x-course-card
                            :course="$course"
                            :href="route('student.courses.show', $course)"
                            :isEnrolled="true"
                        />
                    @endforeach
                </div>
            @else
                <x-empty-state
                    title="No active courses yet"
                    description="You are not enrolled in any classes. Browse the catalog to find subjects that interest you!"
                    icon="academic-cap"
                >
                    <a
                        href="{{ route('student.catalog.index') }}"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors"
                    >
                        Browse Course Catalog
                    </a>
                </x-empty-state>
            @endif
        </div>
    </div>
</x-layouts.app>
