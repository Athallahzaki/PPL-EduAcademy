@props(['course', 'title' => null])

<x-layouts.app :title="$title ?? $course->title">
    <div class="space-y-6">
        <!-- Course Hub Header Card -->
        <div class="overflow-hidden rounded-2xl bg-white border border-gray-200/80 shadow-xs">
            <div class="p-6 flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Banner Preview -->
                <div class="w-full md:w-56 shrink-0 rounded-xl overflow-hidden shadow-xs">
                    <x-course-banner :course="$course" />
                </div>

                <!-- Info and Badges -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        @if($course->subject)
                            <x-badge variant="indigo">{{ $course->subject->name }}</x-badge>
                        @endif

                        <x-badge :variant="$course->level->badgeColor()">
                            {{ $course->level->label() }}
                        </x-badge>

                        <x-badge :variant="$course->status->badgeColor()">
                            {{ $course->status->label() }}
                        </x-badge>

                        <span class="text-xs text-gray-500 font-medium">
                            • {{ $course->enrollments_count ?? $course->enrollments()->count() }} {{ \Illuminate\Support\Str::plural('student', $course->enrollments_count ?? $course->enrollments()->count()) }} enrolled
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 truncate">
                        {{ $course->title }}
                    </h1>

                    <p class="mt-1 text-xs text-gray-500">
                        Created on {{ $course->created_at->format('M d, Y') }} WIB • Last updated {{ $course->updated_at->diffForHumans() }}
                    </p>
                </div>

                <!-- Status & Edit Actions -->
                <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto shrink-0 justify-start md:justify-end pt-2 md:pt-0 border-t md:border-t-0 border-gray-100">
                    <!-- Status Actions Based on Current Status (§6.3) -->
                    @if ($course->status === \App\Enums\CourseStatus::Draft)
                        <!-- Publish Button -->
                        <form method="POST" action="{{ route('instructor.courses.status', $course) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="published">
                            <x-button type="submit" variant="success" size="sm">
                                Publish Class
                            </x-button>
                        </form>

                        <!-- Edit Button -->
                        <a
                            href="{{ route('instructor.courses.edit', $course) }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-xs hover:bg-gray-50 transition-colors"
                        >
                            Edit
                        </a>

                        <!-- Delete Button (Only if 0 enrollments) -->
                        @if (($course->enrollments_count ?? $course->enrollments()->count()) === 0)
                            <form method="POST" action="{{ route('instructor.courses.destroy', $course) }}" onsubmit="return confirm('Delete this draft class permanently?');">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="text-xs text-rose-600 hover:text-rose-700 font-semibold px-2 py-1.5 hover:underline"
                                >
                                    Delete
                                </button>
                            </form>
                        @endif

                    @elseif ($course->status === \App\Enums\CourseStatus::Published)
                        <!-- Archive Button -->
                        <form method="POST" action="{{ route('instructor.courses.status', $course) }}" onsubmit="return confirm('Archive this class? No new students can enroll, but enrolled students retain access.');">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="archived">
                            <x-button type="submit" variant="secondary" size="sm">
                                Archive
                            </x-button>
                        </form>

                        <!-- Move to Draft Button -->
                        @php
                            $enrollmentsCount = $course->enrollments_count ?? $course->enrollments()->count();
                        @endphp
                        @if ($enrollmentsCount === 0)
                            <form method="POST" action="{{ route('instructor.courses.status', $course) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="draft">
                                <x-button type="submit" variant="secondary" size="sm">
                                    Move to Draft
                                </x-button>
                            </form>
                        @else
                            <button
                                type="button"
                                disabled
                                title="Students are enrolled; cannot revert to draft"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-400 cursor-not-allowed opacity-60"
                            >
                                Move to Draft
                            </button>
                        @endif

                        <!-- Edit Button -->
                        <a
                            href="{{ route('instructor.courses.edit', $course) }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-xs hover:bg-gray-50 transition-colors"
                        >
                            Edit
                        </a>

                    @elseif ($course->status === \App\Enums\CourseStatus::Archived)
                        <!-- Republish Button -->
                        <form method="POST" action="{{ route('instructor.courses.status', $course) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="published">
                            <x-button type="submit" variant="success" size="sm">
                                Republish Class
                            </x-button>
                        </form>

                        <!-- Edit Button -->
                        <a
                            href="{{ route('instructor.courses.edit', $course) }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-xs hover:bg-gray-50 transition-colors"
                        >
                            Edit
                        </a>
                    @endif
                </div>
            </div>

            <!-- Tab Navigation Bar -->
            <div class="px-6 bg-gray-50/50">
                <x-course-tabs :course="$course" role="instructor" />
            </div>
        </div>

        <!-- Tab Page Content Slot -->
        <div>
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>
