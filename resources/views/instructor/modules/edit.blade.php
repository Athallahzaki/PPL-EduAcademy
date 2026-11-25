<x-instructor.course-layout :course="$course" title="Edit Module - {{ $module->title }}">
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Edit Module</h2>
                <p class="text-xs text-gray-500">Update title or description for Module #{{ $module->position }}.</p>
            </div>
            <a href="{{ route('instructor.courses.modules.index', $course) }}" class="text-xs text-gray-600 hover:underline">
                &larr; Back to Curriculum
            </a>
        </div>

        <x-card>
            <form action="{{ route('instructor.courses.modules.update', [$course, $module]) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="title" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Module Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" required maxlength="150" value="{{ old('title', $module->title) }}"
                        class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Description (Optional)
                    </label>
                    <textarea name="description" id="description" rows="3" maxlength="1000"
                        class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $module->description) }}</textarea>
                    <p class="text-[11px] text-gray-400 mt-1">Plain text summary, maximum 1000 characters.</p>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('instructor.courses.modules.index', $course) }}"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-instructor.course-layout>
