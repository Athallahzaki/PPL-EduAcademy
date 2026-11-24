<x-layouts.app title="Class Catalog">
    <div class="space-y-6">
        <!-- Header -->
        <x-page-header title="Class Catalog" description="Discover and enroll in online classes taught by expert instructors across diverse subjects." />

        <!-- Filter Bar -->
        <x-filter-bar
            :action="route('student.catalog.index')"
            :active="request()->hasAny(['q', 'subject', 'level'])"
            cols="lg:grid-cols-4"
        >
            <x-search-field class="lg:col-span-2" label="Search" placeholder="Search by class title or keywords..." />

            <div>
                <x-label for="subject" value="Subject" />
                <x-select id="subject" name="subject">
                    <option value="">All Subjects</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(request('subject') == $subject->id)>{{ $subject->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <x-label for="level" value="Difficulty Level" />
                <x-select id="level" name="level">
                    <option value="">All Levels</option>
                    <option value="beginner" @selected(request('level') === 'beginner')>Beginner</option>
                    <option value="intermediate" @selected(request('level') === 'intermediate')>Intermediate</option>
                    <option value="advanced" @selected(request('level') === 'advanced')>Advanced</option>
                </x-select>
            </div>
        </x-filter-bar>

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
                <x-button :href="route('student.catalog.index')" size="sm">
Reset all filters
</x-button>
            </x-empty-state>
        @endif
    </div>
</x-layouts.app>

