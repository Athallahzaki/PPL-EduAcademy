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
                    <x-button :href="route('student.catalog.index')" size="sm">
Browse Course Catalog
</x-button>
                </x-empty-state>
            @endif
        </div>

        <!-- PBI-14: Diskusi Forum Populer (Mockup Bab 5.3 No 10) -->
        <div class="space-y-4 pt-4 border-t border-gray-200/80">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <x-icon name="chat-alt-2" class="h-5 w-5 text-indigo-600" />
                        <span>Community Forum Discussions</span>
                    </h2>
                    <p class="text-xs text-gray-500">Latest topics from fellow students and instructors.</p>
                </div>

                <div class="flex items-center gap-2">
                    <x-button :href="route('forum.threads.create')" variant="secondary" size="sm">
Start a Discussion
</x-button>
                    <x-button :href="route('forum.threads.index')" size="sm">
Open Forum &rarr;
</x-button>
                </div>
            </div>

            @if ($recentThreads->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($recentThreads as $thread)
                        <div class="bg-white rounded-xl border border-gray-200/80 p-4 shadow-2xs hover:border-indigo-200 hover:shadow-xs transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between text-[11px] text-gray-500 mb-2">
                                    <x-badge variant="indigo" size="sm">
                                        {{ $thread->subject?->name ?? 'General' }}
                                    </x-badge>
                                    <span>{{ $thread->created_at->diffForHumans() }}</span>
                                </div>
                                <h4 class="font-bold text-sm text-gray-900 hover:text-indigo-600 line-clamp-2">
                                    <a href="{{ route('forum.threads.show', $thread) }}">
                                        {{ $thread->title }}
                                    </a>
                                </h4>
                                <p class="text-xs text-gray-500 line-clamp-2 mt-1">
                                    {{ Str::limit(strip_tags($thread->content), 100) }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500">
                                <span class="font-medium text-gray-700 truncate max-w-[140px]">
                                    {{ $thread->user?->name }}
                                </span>
                                <a href="{{ route('forum.threads.show', $thread) }}" class="font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                    Read &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border border-dashed border-gray-200 bg-white p-6 text-center text-xs text-gray-500">
                    No recent discussion topics yet. Start the first one now!
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
