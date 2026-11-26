<x-instructor.course-layout :course="$course" title="Curriculum - {{ $course->title }}">
    <div class="space-y-6">
        {{-- Toolbar / Info Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-xs">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Course Curriculum</h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Organize your class into modules and chapters. Items start as drafts and only become visible to enrolled students when both the module and chapter are published.
                </p>
            </div>
            <div>
                <a href="{{ route('instructor.courses.modules.create', $course) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Module
                </a>
            </div>
        </div>

        {{-- Modules List --}}
        @if($modules->isEmpty())
            <x-empty-state
                title="No modules yet"
                description="Start building your class curriculum by adding your first module. Modules group your lessons and chapters together."
                icon="collection">
                <div class="mt-4">
                    <a href="{{ route('instructor.courses.modules.create', $course) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                        + Add First Module
                    </a>
                </div>
            </x-empty-state>
        @else
            <div class="space-y-5">
                @foreach($modules as $module)
                    @include('instructor.modules._module-card', ['course' => $course, 'module' => $module, 'loop' => $loop])
                @endforeach
            </div>
        @endif
    </div>
</x-instructor.course-layout>
