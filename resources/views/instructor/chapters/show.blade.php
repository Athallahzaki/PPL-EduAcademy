<x-instructor.course-layout :course="$course" title="Preview: {{ $chapter->title }}">
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Preview Banner (§6.4) --}}
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-amber-900 shadow-xs">
            <div class="flex items-center gap-3">
                <span class="p-2 bg-amber-100 rounded-lg text-amber-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </span>
                <div>
                    <h4 class="text-sm font-bold">Instructor Preview Mode</h4>
                    <p class="text-xs text-amber-700">
                        Students see this chapter only when both this chapter and Module are published.
                        Current status:
                        <strong>{{ $chapter->is_published ? 'Chapter Published' : 'Chapter Draft' }}</strong> ·
                        <strong>{{ $module->is_published ? 'Module Published' : 'Module Draft' }}</strong>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-end sm:self-auto">
                <a href="{{ route('instructor.courses.modules.chapters.edit', [$course, $module, $chapter]) }}"
                    class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    Edit Chapter
                </a>
                <a href="{{ route('instructor.courses.modules.index', $course) }}"
                    class="px-3 py-1.5 bg-white hover:bg-amber-100/50 text-amber-800 border border-amber-300 text-xs font-semibold rounded-lg transition-colors">
                    Back to Curriculum
                </a>
            </div>
        </div>

        {{-- Content Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
            {{-- Breadcrumb & Title --}}
            <div class="border-b border-gray-100 pb-5">
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                    <span>{{ $course->title }}</span>
                    <span>&rsaquo;</span>
                    <span>{{ $module->title }}</span>
                    <span>&rsaquo;</span>
                    <span class="font-semibold text-gray-900">{{ $chapter->title }}</span>
                </nav>

                <div class="flex items-center gap-3">
                    <x-chapter-type-badge :type="$chapter->type" />
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $chapter->title }}</h1>
                </div>
            </div>

            {{-- Chapter Content (Shared partial) --}}
            @include('chapters._content', ['chapter' => $chapter])
        </div>
    </div>
</x-instructor.course-layout>
