<x-layouts.app title="My Classes">
    <div class="space-y-6">
        <!-- Header -->
        <x-page-header title="My Classes" description="Create and manage your educational courses, curriculum modules, and enrolled students.">
            <x-slot:actions>
                <a
                    href="{{ route('instructor.courses.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Class
                </a>
            </x-slot:actions>
        </x-page-header>

        <!-- Status Filter Chips (§6.1) -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a
                href="{{ route('instructor.courses.index', array_filter(['q' => request('q')])) }}"
                class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all {{ !request('status') ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                All ({{ $totalCount }})
            </a>

            <a
                href="{{ route('instructor.courses.index', array_filter(['status' => 'published', 'q' => request('q')])) }}"
                class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all {{ request('status') === 'published' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 ring-1 ring-inset ring-emerald-600/20' }}"
            >
                Published ({{ $publishedCount }})
            </a>

            <a
                href="{{ route('instructor.courses.index', array_filter(['status' => 'draft', 'q' => request('q')])) }}"
                class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all {{ request('status') === 'draft' ? 'bg-gray-700 text-white shadow-xs' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}"
            >
                Draft ({{ $draftCount }})
            </a>

            <a
                href="{{ route('instructor.courses.index', array_filter(['status' => 'archived', 'q' => request('q')])) }}"
                class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all {{ request('status') === 'archived' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 ring-1 ring-inset ring-amber-600/20' }}"
            >
                Archived ({{ $archivedCount }})
            </a>

            @if(request()->hasAny(['q', 'status']))
                <a
                    href="{{ route('instructor.courses.index') }}"
                    class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-gray-900 underline ml-2"
                >
                    Reset filters
                </a>
            @endif
        </div>

        <!-- Search Bar -->
        <x-card class="p-4">
            <form method="GET" action="{{ route('instructor.courses.index') }}" class="flex flex-col sm:flex-row gap-3">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="flex-1">
                    <x-input
                        id="q"
                        type="search"
                        name="q"
                        :value="request('q')"
                        placeholder="Search your classes by title..."
                    />
                </div>
                <x-button type="submit" size="md">
                    Search
                </x-button>
            </form>
        </x-card>

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
                            <span class="text-gray-400">—</span>
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
                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold">
                        <div class="flex items-center justify-end gap-2">
                            <a
                                href="{{ route('instructor.courses.show', $course) }}"
                                class="rounded-lg bg-indigo-50 px-2.5 py-1 text-indigo-700 hover:bg-indigo-100 transition-colors"
                            >
                                Manage
                            </a>
                            <a
                                href="{{ route('instructor.courses.edit', $course) }}"
                                class="rounded-lg border border-gray-300 bg-white px-2.5 py-1 text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <x-table.empty colspan="7" message="No classes found." description="Create a new class to start building your course curriculum.">
                    <x-slot>
                        <a
                            href="{{ route('instructor.courses.create') }}"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700"
                        >
                            Create your first class
                        </a>
                    </x-slot>
                </x-table.empty>
            @endforelse
        </x-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $courses->links() }}
        </div>
    </div>
</x-layouts.app>
