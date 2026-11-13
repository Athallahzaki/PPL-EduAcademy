<x-layouts.app title="Instructor Dashboard">
    <div class="space-y-6">
        @foreach ($announcements as $announcement)
            <x-announcement-banner :announcement="$announcement" />
        @endforeach

        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-indigo-700 to-indigo-900 rounded-2xl p-6 sm:p-8 text-white shadow-sm">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Welcome, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-indigo-100 max-w-2xl text-sm leading-relaxed">
                Welcome to your Instructor Teaching Hub. Here you can create and manage your courses, grade assignments, interact with students, and conduct live learning sessions.
            </p>
        </div>

        <!-- Instructor Placeholder Modules Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-card>
                <div class="flex items-center gap-3 mb-3">
                    <span class="rounded-lg bg-indigo-50 p-2.5 text-indigo-600">
                        <x-icon name="book-open" class="h-6 w-6" />
                    </span>
                    <h3 class="text-base font-semibold text-gray-900">My Classes</h3>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed">
                    View classes you teach, organize curriculum modules, and manage enrolled learners. Class management tools will be available in Sprint 2.
                </p>
            </x-card>

            <x-card>
                <div class="flex items-center gap-3 mb-3">
                    <span class="rounded-lg bg-amber-50 p-2.5 text-amber-600">
                        <x-icon name="clipboard-list" class="h-6 w-6" />
                    </span>
                    <h3 class="text-base font-semibold text-gray-900">Assignments & Grading</h3>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Review incoming student task submissions, provide detailed constructive feedback, and award marks. Scheduled for Sprint 2.
                </p>
            </x-card>

            <x-card>
                <div class="flex items-center gap-3 mb-3">
                    <span class="rounded-lg bg-emerald-50 p-2.5 text-emerald-600">
                        <x-icon name="video-camera" class="h-6 w-6" />
                    </span>
                    <h3 class="text-base font-semibold text-gray-900">Live Sessions</h3>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Schedule real-time interactive lectures and office hours with students. Scheduled for Sprint 3.
                </p>
            </x-card>
        </div>
    </div>
</x-layouts.app>

