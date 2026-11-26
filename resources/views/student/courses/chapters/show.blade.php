<x-student.course-layout :course="$course" title="{{ $chapter->title }} - {{ $course->title }}">
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Navigation & Breadcrumb --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <nav class="flex items-center gap-2 text-xs text-gray-500 overflow-x-auto py-1">
                <a href="{{ route('student.courses.show', $course) }}" class="hover:text-indigo-600 hover:underline flex-shrink-0">
                    {{ $course->title }}
                </a>
                <span>&rsaquo;</span>
                <a href="{{ route('student.courses.modules.index', $course) }}" class="hover:text-indigo-600 hover:underline flex-shrink-0">
                    {{ $module->title }}
                </a>
                <span>&rsaquo;</span>
                <span class="font-semibold text-gray-900 truncate">
                    {{ $chapter->title }}
                </span>
            </nav>

            <a href="{{ route('student.courses.modules.index', $course) }}"
                class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 self-start sm:self-auto flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Back to Content Outline
            </a>
        </div>

        {{-- Main Chapter Reading Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
            {{-- Header --}}
            <div class="border-b border-gray-100 pb-5">
                <div class="flex items-center gap-2.5 mb-2">
                    <x-chapter-type-badge :type="$chapter->type" />
                    <span class="text-xs text-gray-400">Chapter #{{ $chapter->position }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $chapter->title }}</h1>
            </div>

            {{-- Content Section (Shared Partial) --}}
            @include('chapters._content', ['chapter' => $chapter])
        </div>

        {{-- Bottom Prev / Next Navigation Bar (§7.2) --}}
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                @if($previousChapter)
                    <a href="{{ route('student.courses.modules.chapters.show', [$course, $previousChapter->module, $previousChapter]) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-xs font-semibold text-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>Previous: <strong class="text-gray-900">{{ \Illuminate\Support\Str::limit($previousChapter->title, 25) }}</strong></span>
                    </a>
                @endif
            </div>

            <div>
                <a href="{{ route('student.courses.modules.index', $course) }}"
                    class="text-xs text-gray-500 hover:text-gray-700 hover:underline">
                    All Lessons
                </a>
            </div>

            <div>
                @if($nextChapter)
                    <a href="{{ route('student.courses.modules.chapters.show', [$course, $nextChapter->module, $nextChapter]) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                        <span>Next: <strong>{{ \Illuminate\Support\Str::limit($nextChapter->title, 25) }}</strong></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-student.course-layout>
