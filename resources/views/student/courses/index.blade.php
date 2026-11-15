<x-layouts.app title="My Classes">
    <div class="space-y-6">
        <!-- Header -->
        <x-page-header title="My Classes" description="View and continue learning from all the courses you have enrolled in.">
            <x-slot:actions>
                <a
                    href="{{ route('student.catalog.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Browse Catalog
                </a>
            </x-slot:actions>
        </x-page-header>

        <!-- Course Cards Grid (§7.4) -->
        @if ($courses->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($courses as $course)
                    <x-course-card
                        :course="$course"
                        :href="route('student.courses.show', $course)"
                        :isEnrolled="true"
                    />
                @endforeach
            </div>

            <div class="mt-6">
                {{ $courses->links() }}
            </div>
        @else
            <x-empty-state
                title="You haven't enrolled in any classes yet"
                description="Explore our wide variety of courses in the class catalog and start your learning journey today!"
                icon="academic-cap"
            >
                <a
                    href="{{ route('student.catalog.index') }}"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors"
                >
                    Explore Class Catalog
                </a>
            </x-empty-state>
        @endif
    </div>
</x-layouts.app>
