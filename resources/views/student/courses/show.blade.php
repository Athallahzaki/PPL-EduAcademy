<x-student.course-layout :course="$course" title="Overview — {{ $course->title }}">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main: Overview & Future Content Card -->
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <x-slot:header>
                    <h3 class="text-base font-bold text-gray-900">Course Overview</h3>
                </x-slot:header>

                <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $course->description }}
                </div>
            </x-card>

            <!-- Learning Content Empty State (§7.5: No fake data) -->
            <x-empty-state
                title="Learning content will appear here"
                description="Your instructor is preparing lesson modules, video lectures, and quizzes for this class (Sprint 3: PBI-12 & PBI-13). Check back soon!"
                icon="collection"
            />
        </div>

        <!-- Right Sidebar: Instructor Info -->
        <div class="space-y-6">
            @if ($course->instructor)
                <x-card>
                    <x-slot:header>
                        <h3 class="text-base font-bold text-gray-900">Your Instructor</h3>
                    </x-slot:header>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 font-bold text-sm shadow-xs">
                                {{ substr($course->instructor->name, 0, 1) }}
                            </span>
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm">
                                    <x-user-name :user="$course->instructor" />
                                </h4>
                                @if($course->instructor->instructorProfile?->expertise)
                                    <p class="text-indigo-600 font-medium">{{ $course->instructor->instructorProfile->expertise }}</p>
                                @endif
                            </div>
                        </div>

                        @if ($course->instructor->instructorProfile?->bio)
                            <p class="text-gray-600 leading-relaxed whitespace-pre-line pt-1 border-t border-gray-100">
                                {{ $course->instructor->instructorProfile->bio }}
                            </p>
                        @endif

                        @if ($course->instructor->instructorProfile?->portfolio_url)
                            <div class="pt-2">
                                <a
                                    href="{{ $course->instructor->instructorProfile->portfolio_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-indigo-600 hover:underline inline-flex items-center gap-1 font-semibold"
                                >
                                    Portfolio Link &nearr;
                                </a>
                            </div>
                        @endif
                    </div>
                </x-card>
            @endif

            <x-card>
                <x-slot:header>
                    <h3 class="text-base font-bold text-gray-900">Class Information</h3>
                </x-slot:header>

                <dl class="space-y-3 text-xs">
                    <div>
                        <dt class="font-semibold text-gray-400 uppercase tracking-wider">Subject</dt>
                        <dd class="mt-1">
                            <x-badge variant="indigo">{{ $course->subject?->name ?? 'Uncategorized' }}</x-badge>
                        </dd>
                    </div>

                    <div>
                        <dt class="font-semibold text-gray-400 uppercase tracking-wider">Difficulty Level</dt>
                        <dd class="mt-1">
                            <x-badge :variant="$course->level->badgeColor()">{{ $course->level->label() }}</x-badge>
                        </dd>
                    </div>

                    <div>
                        <dt class="font-semibold text-gray-400 uppercase tracking-wider">Classmates</dt>
                        <dd class="mt-1 font-bold text-indigo-600">
                            {{ $course->enrollments_count }} {{ \Illuminate\Support\Str::plural('student', $course->enrollments_count) }} enrolled
                        </dd>
                    </div>
                </dl>
            </x-card>
        </div>
    </div>
</x-student.course-layout>

