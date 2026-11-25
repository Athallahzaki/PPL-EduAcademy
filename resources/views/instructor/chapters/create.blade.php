<x-instructor.course-layout :course="$course" title="Create Chapter - {{ $module->title }}">
    <div class="max-w-3xl mx-auto space-y-6"
        x-data="{
            type: '{{ old('type', 'text') }}',
            videoUrl: '{{ old('video_url', '') }}',
            isYouTubeOrVimeo(url) {
                if (!url) return true;
                const lower = url.toLowerCase();
                return lower.includes('youtube.com') || lower.includes('youtu.be') || lower.includes('vimeo.com');
            }
        }">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Add Chapter</h2>
                <p class="text-xs text-gray-500">Adding to Module: <strong>{{ $module->title }}</strong></p>
            </div>
            <a href="{{ route('instructor.courses.modules.index', $course) }}" class="text-xs text-gray-600 hover:underline">
                &larr; Back to Curriculum
            </a>
        </div>

        <x-card>
            <form action="{{ route('instructor.courses.modules.chapters.store', [$course, $module]) }}" method="POST" class="space-y-5">
                @csrf

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Chapter Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" required maxlength="150" value="{{ old('title') }}"
                        class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="e.g. Setting Up Your Development Environment">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type Selection --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                        Content Type <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-3 p-3.5 border rounded-lg cursor-pointer transition-all"
                            :class="type === 'text' ? 'border-indigo-600 bg-indigo-50/50 ring-1 ring-indigo-600' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="type" value="text" x-model="type" class="text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <span class="text-sm font-semibold text-gray-900 block">Text Lesson</span>
                                <span class="text-xs text-gray-500">Rich formatted content written in Markdown</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 border rounded-lg cursor-pointer transition-all"
                            :class="type === 'video' ? 'border-indigo-600 bg-indigo-50/50 ring-1 ring-indigo-600' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="type" value="video" x-model="type" class="text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <span class="text-sm font-semibold text-gray-900 block">Video Lesson</span>
                                <span class="text-xs text-gray-500">Embedded video link with optional notes</span>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Video URL (if type === 'video') --}}
                <div x-show="type === 'video'" x-cloak class="space-y-2">
                    <label for="video_url" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Video URL <span class="text-red-500">*</span>
                    </label>
                    <input type="url" name="video_url" id="video_url" maxlength="500"
                        x-model="videoUrl"
                        value="{{ old('video_url') }}"
                        class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                    <p class="text-[11px] text-gray-500">
                        Supports YouTube (standard, shortened, shorts) and Vimeo links for inline video playback. Other URLs will be presented as an external link.
                    </p>
                    <div x-show="videoUrl && !isYouTubeOrVimeo(videoUrl)" x-cloak class="p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-800">
                        Notice: This link cannot be embedded inline and will be shown to students as an external link button.
                    </div>
                    @error('video_url')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Markdown Body --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="body" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <span x-text="type === 'text' ? 'Chapter Content (Markdown)' : 'Lesson Notes (Markdown, Optional)'"></span>
                            <span x-show="type === 'text'" class="text-red-500">*</span>
                        </label>
                    </div>

                    <textarea name="body" id="body" rows="12" maxlength="50000"
                        class="w-full font-mono text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Write your lesson content using Markdown...">{{ old('body') }}</textarea>

                    @error('body')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Markdown Help Cheat Sheet --}}
                    <x-markdown-help />
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('instructor.courses.modules.index', $course) }}"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                        Create Chapter
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-instructor.course-layout>
