<x-layouts.app title="My Classes">
    <div class="space-y-6">
        <!-- Page Header -->
        <x-page-header title="My Classes" description="Create and manage your educational courses, curriculum modules, and enrolled students.">
            <x-slot:actions>
                <x-button :href="route('instructor.courses.create')">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Class
                </x-button>
            </x-slot:actions>
        </x-page-header>

        <!-- Status Chips -->
        <div class="flex flex-wrap items-center gap-2.5">
            <x-filter-chip
                :href="route('instructor.courses.index', array_filter(['q' => request('q')]))"
                tone="indigo"
                :active="! request('status')"
                :count="$totalCount"
            >All</x-filter-chip>

            <x-filter-chip
                :href="route('instructor.courses.index', array_filter(['status' => 'published', 'q' => request('q')]))"
                tone="emerald"
                dot
                :active="request('status') === 'published'"
                :count="$publishedCount"
            >Published</x-filter-chip>

            <x-filter-chip
                :href="route('instructor.courses.index', array_filter(['status' => 'draft', 'q' => request('q')]))"
                tone="gray"
                dot
                :active="request('status') === 'draft'"
                :count="$draftCount"
            >Draft</x-filter-chip>

            <x-filter-chip
                :href="route('instructor.courses.index', array_filter(['status' => 'archived', 'q' => request('q')]))"
                tone="amber"
                dot
                :active="request('status') === 'archived'"
                :count="$archivedCount"
            >Archived</x-filter-chip>
        </div>

        <!-- Filter Bar -->
        <x-filter-bar
            :action="route('instructor.courses.index')"
            :active="request()->hasAny(['q', 'status'])"
            cols="lg:grid-cols-1"
        >
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <x-search-field label="Search" placeholder="Search your classes by title..." />
        </x-filter-bar>

        <!-- Classes Table -->
        <x-table>
            <x-slot:headers>
                <th scope="col" class="px-6 py-3.5">Class</th>
                <th scope="col" class="px-6 py-3.5">Subject</th>
                <th scope="col" class="px-6 py-3.5">Level</th>
                <th scope="col" class="px-6 py-3.5">Status</th>
                <th scope="col" class="px-6 py-3.5 text-center">Students</th>
                <th scope="col" class="px-6 py-3.5">Updated</th>
                <th scope="col" class="px-6 py-3.5 text-right">Actions</th>
            </x-slot:headers>

            @forelse ($courses as $course)
                <tr class="hover:bg-gray-50/75 transition-colors">
                    <!-- Banner + Title -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3.5">
                            <div class="h-12 w-20 shrink-0 overflow-hidden rounded-lg shadow-xs">
                                <x-course-banner :course="$course" />
                            </div>
                            <div class="min-w-0 max-w-xs sm:max-w-sm">
                                <a
                                    href="{{ route('instructor.courses.show', $course) }}"
                                    class="font-bold text-sm text-gray-900 hover:text-indigo-600 transition-colors line-clamp-1"
                                >
                                    {{ $course->title }}
                                </a>
                                <p class="text-xs text-gray-400 mt-0.5 truncate">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($course->description), 60) }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <!-- Subject -->
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">
                        @if($course->subject)
                            <x-badge variant="indigo">{{ $course->subject->name }}</x-badge>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>

                    <!-- Level -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge :variant="$course->level->badgeColor()">
                            {{ $course->level->label() }}
                        </x-badge>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge :variant="$course->status->badgeColor()">
                            {{ $course->status->label() }}
                        </x-badge>
                    </td>

                    <!-- Students Count -->
                    <td class="px-6 py-4 whitespace-nowrap text-center font-semibold text-xs text-gray-900">
                        {{ $course->enrollments_count }}
                    </td>

                    <!-- Updated At -->
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                        {{ $course->updated_at->format('M d, Y') }}
                    </td>

                    <!-- Actions -->
                <x-table.actions>
                    <x-row-action :href="route('instructor.courses.show', $course)" variant="primary">Manage</x-row-action>
                    <x-row-action :href="route('instructor.courses.edit', $course)">Edit</x-row-action>
                </x-table.actions>
                </tr>
            @empty
                <x-table.empty colspan="7" message="No classes found." description="Create a new class to start building your course curriculum.">
                    <x-button :href="route('instructor.courses.create')" size="sm">Create your first class</x-button>
                </x-table.empty>
            @endforelse
        </x-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $courses->links() }}
        </div>
    </div>
</x-layouts.app>

