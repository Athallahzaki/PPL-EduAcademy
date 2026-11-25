<x-instructor.course-layout :course="$course" title="Overview - {{ $course->title }}">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Description -->
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <x-slot:header>
                    <h3 class="text-base font-bold text-gray-900">About this Class</h3>
                </x-slot:header>

                <!-- Plain text escaped with preserved line breaks (§1.9 / §13) -->
                <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $course->description }}
                </div>
            </x-card>

            <!-- Curriculum Overview Card (§6.7) -->
            <x-card>
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900">Curriculum</h3>
                        <a href="{{ route('instructor.courses.modules.index', $course) }}"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                            Go to Curriculum &rarr;
                        </a>
                    </div>
                </x-slot:header>

                <div class="space-y-4">
                    <p class="text-sm text-gray-700">
                        <strong>Modules:</strong> {{ $modulesCount ?? 0 }} ({{ $publishedModulesCount ?? 0 }} published)
                        <span class="text-gray-300 mx-2">&middot;</span>
                        <strong>Chapters:</strong> {{ $chaptersCount ?? 0 }} ({{ $publishedChaptersCount ?? 0 }} published)
                    </p>

                    <div>
                        <a href="{{ route('instructor.courses.modules.index', $course) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg border border-indigo-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            Manage Modules & Chapters
                        </a>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Sidebar Meta Details -->
        <div class="space-y-6">
            <x-card>
                <x-slot:header>
                    <h3 class="text-base font-bold text-gray-900">Class Details</h3>
                </x-slot:header>

                <dl class="space-y-4 text-xs">
                    <div>
                        <dt class="font-semibold text-gray-400 uppercase tracking-wider">Subject Category</dt>
                        <dd class="mt-1 font-medium text-gray-900 flex items-center gap-1.5">
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
                        <dt class="font-semibold text-gray-400 uppercase tracking-wider">Current Status</dt>
                        <dd class="mt-1">
                            <x-badge :variant="$course->status->badgeColor()">{{ $course->status->label() }}</x-badge>
                        </dd>
                    </div>

                    <div>
                        <dt class="font-semibold text-gray-400 uppercase tracking-wider">Enrolled Students</dt>
                        <dd class="mt-1 font-bold text-sm text-indigo-600">
                            {{ $course->enrollments_count }} {{ \Illuminate\Support\Str::plural('student', $course->enrollments_count) }}
                        </dd>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <dt class="font-semibold text-gray-400 uppercase tracking-wider">Created</dt>
                        <dd class="mt-0.5 text-gray-600">{{ $course->created_at->format('M d, Y H:i') }} WIB</dd>
                    </div>

                    <div>
                        <dt class="font-semibold text-gray-400 uppercase tracking-wider">Last Modified</dt>
                        <dd class="mt-0.5 text-gray-600">{{ $course->updated_at->format('M d, Y H:i') }} WIB</dd>
                    </div>
                </dl>
            </x-card>
        </div>
    </div>
</x-instructor.course-layout>

