<x-layouts.app title="Class Catalog">
    <div class="space-y-6">
        <!-- Header -->
        <x-page-header title="Class Catalog" description="Discover and enroll in online classes taught by expert instructors across diverse subjects." />

        <!-- Filters Form -->
        <x-card class="p-4 sm:p-5">
            <form method="GET" action="{{ route('student.catalog.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <!-- Search Title -->
                <div class="sm:col-span-2 lg:col-span-2">
                    <x-label for="q" value="Search Classes" />
                    <x-input
                        id="q"
                        type="search"
                        name="q"
                        :value="request('q')"
                        placeholder="Search by class title or keywords..."
                    />
                </div>

                <!-- Subject Filter -->
                <div>
                    <x-label for="subject" value="Subject" />
                    <x-select id="subject" name="subject">
                        <option value="">All Subjects</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </x-select>
                </div>

                <!-- Level Filter & Actions -->
                <div>
                    <x-label for="level" value="Difficulty Level" />
                    <div class="flex items-center gap-2">
                        <x-select id="level" name="level" class="flex-1">
                            <option value="">All Levels</option>
                            <option value="beginner" {{ request('level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ request('level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ request('level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </x-select>

                        <x-button type="submit" size="md">
                            Filter
                        </x-button>
                    </div>
                </div>
            </form>

            @if(request()->hasAny(['q', 'subject', 'level']))
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Filters applied</span>
                    <a href="{{ route('student.catalog.index') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 underline">
                        Clear all filters
                    </a>
                </div>
            @endif
        </x-card>

        <!-- Course Grid (1 col mobile, 2 tablet, 3 desktop §13) -->
        @if ($courses->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($courses as $course)
                    <x-course-card
                        :course="$course"
                        :href="route('student.catalog.show', $course)"
                        :isEnrolled="in_array($course->id, $enrolledCourseIds)"
                    />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $courses->links() }}
            </div>
        @else
            <x-empty-state
                title="No classes match your search"
                description="Try adjusting your keywords, subject category, or difficulty level filters."
                icon="book-open"
            >
                <a
                    href="{{ route('student.catalog.index') }}"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors"
                >
                    Reset all filters
                </a>
            </x-empty-state>
        @endif
    </div>
</x-layouts.app>

