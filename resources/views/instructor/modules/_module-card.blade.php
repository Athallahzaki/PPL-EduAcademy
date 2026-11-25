@props(['course', 'module', 'loop'])

<div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden transition-all">
    {{-- Module Header --}}
    <div class="p-5 bg-gray-50/75 border-b border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-gray-200 text-gray-700">
                    Module #{{ $module->position }}
                </span>

                <h3 class="text-base font-bold text-gray-900">{{ $module->title }}</h3>

                @if($module->is_published)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Draft
                    </span>
                @endif

                <span class="text-xs text-gray-500">
                    ({{ $module->chapters->count() }} {{ \Illuminate\Support\Str::plural('chapter', $module->chapters->count()) }})
                </span>
            </div>

            @if($module->description)
                <p class="text-xs text-gray-600 max-w-2xl line-clamp-2">{{ $module->description }}</p>
            @endif

            @if(!$module->is_published)
                <p class="text-xs text-amber-700 flex items-center gap-1 font-medium">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Draft module — all chapters inside remain hidden from students until this module is published.
                </p>
            @endif
        </div>

        {{-- Module Actions --}}
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Reorder --}}
            <x-reorder-buttons
                :up-action="route('instructor.courses.modules.move', [$course, $module])"
                :down-action="route('instructor.courses.modules.move', [$course, $module])"
                :is-first="$loop->first"
                :is-last="$loop->last"
            />

            {{-- Publish / Unpublish --}}
            <x-publish-toggle
                :is-published="$module->is_published"
                :action="route('instructor.courses.modules.publish', [$course, $module])"
                label="Module"
            />

            {{-- Add Chapter --}}
            <a href="{{ route('instructor.courses.modules.chapters.create', [$course, $module]) }}"
                class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-md transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Chapter
            </a>

            {{-- Edit Module --}}
            <a href="{{ route('instructor.courses.modules.edit', [$course, $module]) }}"
                class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors"
                title="Edit Module">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>

            {{-- Delete Module with x-modal --}}
            @if($module->canBeDeleted())
                <div x-data="{ showDeleteModal: false }">
                    <button type="button" @click="showDeleteModal = true"
                        class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors"
                        title="Delete Module">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>

                    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showDeleteModal = false"></div>
                        <div class="flex min-h-full items-center justify-center p-4">
                            <div class="relative bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-gray-100 space-y-4">
                                <h3 class="text-base font-bold text-gray-900">Delete Module</h3>
                                <p class="text-sm text-gray-600">
                                    Are you sure you want to delete <strong class="text-gray-900">{{ $module->title }}</strong>?
                                    This will permanently remove the module and all <strong class="text-red-600">{{ $module->chapters->count() }}</strong> chapters inside it.
                                </p>
                                <div class="flex justify-end gap-2 pt-2">
                                    <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg">Cancel</button>
                                    <form action="{{ route('instructor.courses.modules.destroy', [$course, $module]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg">Delete Module</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <button type="button" disabled
                    title="{{ $module->deletionBlockedReason() ?? 'Module cannot be deleted' }}"
                    class="p-1.5 text-gray-300 cursor-not-allowed rounded-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Chapter List --}}
    <div class="p-4 sm:p-5">
        @if($module->chapters->isEmpty())
            <div class="p-6 text-center border-2 border-dashed border-gray-200 rounded-lg">
                <p class="text-xs text-gray-500">No chapters yet in this module.</p>
                <a href="{{ route('instructor.courses.modules.chapters.create', [$course, $module]) }}"
                    class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:underline">
                    + Add the first chapter
                </a>
            </div>
        @else
            <div class="divide-y divide-gray-100 border border-gray-200 rounded-lg overflow-hidden bg-white">
                @foreach($module->chapters as $chapLoop => $chapter)
                    <div class="p-3 sm:px-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50/75 transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            {{-- Chapter Order --}}
                            <span class="text-xs font-mono font-medium text-gray-400 w-5 text-right flex-shrink-0">
                                {{ $loop->iteration }}.
                            </span>

                            <x-chapter-type-badge :type="$chapter->type" />

                            <span class="text-sm font-semibold text-gray-900 truncate" title="{{ $chapter->title }}">
                                {{ $chapter->title }}
                            </span>

                            @if($chapter->is_published && !$module->is_published)
                                <span class="hidden md:inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200" title="Module is draft, so students cannot see this chapter yet">
                                    Hidden — module is draft
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-auto flex-wrap">
                            {{-- Reorder chapter --}}
                            <x-reorder-buttons
                                :up-action="route('instructor.courses.modules.chapters.move', [$course, $module, $chapter])"
                                :down-action="route('instructor.courses.modules.chapters.move', [$course, $module, $chapter])"
                                :is-first="$loop->first"
                                :is-last="$loop->last"
                            />

                            {{-- Publish / Unpublish chapter --}}
                            <x-publish-toggle
                                :is-published="$chapter->is_published"
                                :action="route('instructor.courses.modules.chapters.publish', [$course, $module, $chapter])"
                                label="Chapter"
                            />

                            {{-- Preview Chapter --}}
                            <a href="{{ route('instructor.courses.modules.chapters.show', [$course, $module, $chapter]) }}"
                                class="p-1 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors"
                                title="Preview Chapter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            {{-- Edit Chapter --}}
                            <a href="{{ route('instructor.courses.modules.chapters.edit', [$course, $module, $chapter]) }}"
                                class="p-1 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded transition-colors"
                                title="Edit Chapter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            {{-- Delete Chapter --}}
                            <div x-data="{ showDeleteModal: false }">
                                <button type="button" @click="showDeleteModal = true"
                                    class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded transition-colors"
                                    title="Delete Chapter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>

                                <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                                    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showDeleteModal = false"></div>
                                    <div class="flex min-h-full items-center justify-center p-4">
                                        <div class="relative bg-white rounded-xl max-w-sm w-full p-5 shadow-xl border border-gray-100 space-y-4">
                                            <h3 class="text-sm font-bold text-gray-900">Delete Chapter</h3>
                                            <p class="text-xs text-gray-600">
                                                Delete chapter <strong class="text-gray-900">{{ $chapter->title }}</strong>? This action cannot be undone.
                                            </p>
                                            <div class="flex justify-end gap-2 pt-2">
                                                <button type="button" @click="showDeleteModal = false" class="px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg">Cancel</button>
                                                <form action="{{ route('instructor.courses.modules.chapters.destroy', [$course, $module, $chapter]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
