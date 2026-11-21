<x-layouts.app title="{{ $course->title }}">
    <div class="space-y-6 max-w-5xl mx-auto">
        <!-- Back Navigation -->
        <div>
            <a
                href="{{ route('student.catalog.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-gray-900 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to Class Catalog
            </a>
        </div>

        <!-- Course Header -->
        <div class="overflow-hidden rounded-2xl bg-white border border-gray-200/80 shadow-xs">
            <!-- Full Banner (16:9) -->
            <div class="max-h-80 w-full overflow-hidden">
                <x-course-banner :course="$course" class="max-h-80" />
            </div>

            <!-- Title & Badges -->
            <div class="p-6 sm:p-8">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    @if($course->subject)
                        <x-badge variant="indigo">{{ $course->subject->name }}</x-badge>
                    @endif

                    <x-badge :variant="$course->level->badgeColor()">
                        {{ $course->level->label() }}
                    </x-badge>

                    <span class="text-xs text-gray-500 font-medium">
                        • {{ $course->enrollments_count }} {{ \Illuminate\Support\Str::plural('student', $course->enrollments_count) }} enrolled
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">
                    {{ $course->title }}
                </h1>
            </div>
        </div>

        <!-- 2-Column Content Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Description and Instructor -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Course Overview Description (§1.9: Escaped plain text with whitespace-pre-line) -->
                <x-card>
                    <x-slot:header>
                        <h2 class="text-base font-bold text-gray-900">About this Class</h2>
                    </x-slot:header>

                    <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ $course->description }}
                    </div>
                </x-card>

                <!-- Instructor Profile Block (§7.2) -->
                @if ($course->instructor)
                    <x-card>
                        <x-slot:header>
                            <h2 class="text-base font-bold text-gray-900">About the Instructor</h2>
                        </x-slot:header>

                        <div class="flex items-start gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700 font-bold text-base shadow-xs">
                                {{ substr($course->instructor->name, 0, 1) }}
                            </span>

                            <div class="space-y-1.5 min-w-0">
                                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                    <x-user-name :user="$course->instructor" />
                                </h3>

                                @if ($course->instructor->instructorProfile?->expertise)
                                    <p class="text-xs font-semibold text-indigo-600">
                                        {{ $course->instructor->instructorProfile->expertise }}
                                    </p>
                                @endif

                                @if ($course->instructor->instructorProfile?->bio)
                                    <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line pt-1">
                                        {{ $course->instructor->instructorProfile->bio }}
                                    </p>
                                @endif

                                @if ($course->instructor->instructorProfile?->portfolio_url)
                                    <div class="pt-1">
                                        <a
                                            href="{{ $course->instructor->instructorProfile->portfolio_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-xs text-indigo-600 hover:underline inline-flex items-center gap-1 font-medium"
                                        >
                                            Visit Portfolio &nearr;
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-card>
                @endif
            </div>

            <!-- Right: Enrollment CTA Card -->
            <div class="space-y-6">
                <x-card class="sticky top-24">
                    <x-slot:header>
                        <h3 class="text-base font-bold text-gray-900">Class Enrolment</h3>
                    </x-slot:header>

                    <div class="space-y-4 text-xs">
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <span class="font-medium text-gray-500">Access Fee</span>
                            <span class="text-sm font-extrabold text-emerald-600">Free</span>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <span class="font-medium text-gray-500">Students Enrolled</span>
                            <span class="font-bold text-gray-900">{{ $course->enrollments_count }}</span>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <span class="font-medium text-gray-500">Difficulty Level</span>
                            <x-badge :variant="$course->level->badgeColor()">{{ $course->level->label() }}</x-badge>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <span class="font-medium text-gray-500">Subject</span>
                            <span class="font-semibold text-gray-900">{{ $course->subject?->name ?? '-' }}</span>
                        </div>

                        <!-- CTA Button Form (§7.3) -->
                        <div class="pt-3">
                            @if ($course->isEnrollable())
                                <form method="POST" action="{{ route('student.courses.enroll', $course) }}">
                                    @csrf
                                    <x-button type="submit" size="lg" class="w-full">
                                        Enroll Now (Instant)
                                    </x-button>
                                </form>
                                <p class="mt-2 text-center text-[11px] text-gray-400">
                                    Instant enrollment. You will gain immediate access to this class.
                                </p>
                            @else
                                <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-center text-xs text-amber-800">
                                    This class is currently not accepting new enrollments.
                                </div>
                            @endif
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-layouts.app>

