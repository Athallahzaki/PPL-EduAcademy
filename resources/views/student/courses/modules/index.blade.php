<x-student.course-layout :course="$course" title="Content - {{ $course->title }}">
    <div class="space-y-6">
        {{-- Header Bar --}}
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Course Content Outline</h2>
                <p class="text-xs text-gray-500 mt-0.5">Explore the lessons, reading materials, and video lectures in this course.</p>
            </div>

            @if($firstChapter)
                <div>
                    <a href="{{ route('student.courses.modules.chapters.show', [$course, $firstChapter->module, $firstChapter]) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                        <span>Start Learning</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            @endif
        </div>

        {{-- Outline List --}}
        @if($modules->isEmpty())
            <x-empty-state
                title="No content published yet"
                description="Your instructor has not published any content yet."
                icon="collection"
            />
        @else
            <div class="space-y-5">
                @foreach($modules as $modIndex => $module)
                    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
                        {{-- Module Header --}}
                        <div class="p-4 sm:p-5 bg-gray-50/75 border-b border-gray-200">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">
                                        Module {{ $modIndex + 1 }}
                                    </span>
                                    <h3 class="text-base font-bold text-gray-900 mt-0.5">{{ $module->title }}</h3>
                                </div>
                                <span class="text-xs text-gray-500 bg-white px-2.5 py-1 rounded-md border border-gray-200">
                                    {{ $module->chapters->count() }} {{ \Illuminate\Support\Str::plural('lesson', $module->chapters->count()) }}
                                </span>
                            </div>

                            @if($module->description)
                                <p class="text-xs text-gray-600 mt-2 leading-relaxed">{{ $module->description }}</p>
                            @endif
                        </div>

                        {{-- Chapters in Module --}}
                        @if($module->chapters->isEmpty())
                            <div class="p-4 text-xs text-gray-400 italic">
                                No chapters available in this module yet.
                            </div>
                        @else
                            <div class="divide-y divide-gray-100">
                                @foreach($module->chapters as $chapIndex => $chapter)
                                    <a href="{{ route('student.courses.modules.chapters.show', [$course, $module, $chapter]) }}"
                                        class="p-3 sm:px-5 flex items-center justify-between gap-3 hover:bg-indigo-50/50 transition-colors group">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <span class="text-xs font-mono text-gray-400 w-5 text-right flex-shrink-0">
                                                {{ $chapIndex + 1 }}.
                                            </span>

                                            <x-chapter-type-badge :type="$chapter->type" />

                                            <span class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 truncate">
                                                {{ $chapter->title }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-1 text-xs text-gray-400 group-hover:text-indigo-600 flex-shrink-0">
                                            <span>Read</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-student.course-layout>
