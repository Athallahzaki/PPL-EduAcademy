@props(['course', 'title' => null])

<x-layouts.app :title="$title ?? $course->title">
    <div class="space-y-6">
        <!-- Student Course Header Card -->
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

                        @if ($course->status === \App\Enums\CourseStatus::Archived)
                            <x-badge variant="amber">Archived</x-badge>
                        @endif

                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                            Enrolled
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 truncate">
                        {{ $course->title }}
                    </h1>

                    @if($course->instructor)
                        <div class="mt-2 flex items-center gap-2 text-xs text-gray-600">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-50 font-bold text-[10px] text-indigo-700">
                                {{ substr($course->instructor->name, 0, 1) }}
                            </span>
                            <span>Taught by <strong class="text-gray-900">{{ $course->instructor->name }}</strong></span>
                            @if($course->instructor->isSuspended())
                                <span class="rounded bg-rose-50 px-1.5 py-0.5 text-[10px] font-semibold text-rose-700">Instructor suspended</span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="w-full md:w-auto shrink-0 flex items-center justify-start md:justify-end">
                    <a
                        href="{{ route('student.courses.index') }}"
                        class="text-xs font-medium text-gray-500 hover:text-gray-800 hover:underline"
                    >
                        &larr; Back to My Classes
                    </a>
                </div>
            </div>

            <!-- Tab Navigation Bar -->
            <div class="px-6 bg-gray-50/50">
                <x-course-tabs :course="$course" role="student" />
            </div>
        </div>

        <!-- Tab Page Content Slot -->
        <div>
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>

