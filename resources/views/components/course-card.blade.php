@props([
    'course',
    'href' => null,
    'isEnrolled' => false,
    'showInstructor' => true,
])

<div {{ $attributes->merge(['class' => 'group flex flex-col overflow-hidden rounded-2xl bg-white border border-gray-200/80 shadow-xs hover:shadow-md transition-all duration-200']) }}>
    <!-- Banner Area -->
    <a href="{{ $href }}" class="block overflow-hidden">
        <x-course-banner :course="$course" class="group-hover:opacity-95" />
    </a>

    <!-- Card Content -->
    <div class="flex flex-1 flex-col p-5">
        <!-- Badges Row -->
        <div class="flex flex-wrap items-center gap-2 mb-3">
            @if($course->subject)
                <x-badge variant="indigo">
                    {{ $course->subject->name }}
                </x-badge>
            @endif

            <x-badge :variant="$course->level->badgeColor()">
                {{ $course->level->label() }}
            </x-badge>

            @if($isEnrolled)
                <x-badge variant="success">
                    Enrolled
                </x-badge>
            @endif

            @if($course->status === \App\Enums\CourseStatus::Archived)
                <x-badge variant="amber">
                    Archived
                </x-badge>
            @endif
        </div>

        <!-- Title -->
        <h3 class="text-base font-bold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
            <a href="{{ $href }}">
                {{ $course->title }}
            </a>
        </h3>

        <!-- Instructor Information -->
        @if($showInstructor && $course->instructor)
            <div class="mt-2 flex items-center gap-2 text-xs text-gray-600">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-50 font-bold text-[10px] text-indigo-700">
                    {{ substr($course->instructor->name, 0, 1) }}
                </span>
                <span class="font-medium text-gray-800 line-clamp-1">{{ $course->instructor->name }}</span>
            </div>
        @endif

        <!-- Plain text description snippet (Escaped!) -->
        <p class="mt-2.5 text-xs text-gray-500 line-clamp-2 leading-relaxed">
            {{ \Illuminate\Support\Str::limit(strip_tags($course->description), 120) }}
        </p>

        <!-- Card Footer -->
        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
            <span class="inline-flex items-center gap-1.5 font-medium text-gray-600">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                <span>{{ $course->enrollments_count ?? $course->enrollments()->count() }} {{ \Illuminate\Support\Str::plural('student', $course->enrollments_count ?? $course->enrollments()->count()) }}</span>
            </span>

            <a href="{{ $href }}" class="font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-0.5">
                <span>View</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>
</div>

