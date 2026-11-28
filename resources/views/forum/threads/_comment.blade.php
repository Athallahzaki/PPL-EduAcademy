@php
$currentUser = auth()->user();
$canUpdateComment = $currentUser && ($currentUser->isAdmin() || $currentUser->id === $comment->user_id);
$canDeleteComment = $currentUser && ($currentUser->isAdmin() || $currentUser->id === $comment->user_id);
$authorRole = $comment->user?->role ?? \App\Enums\Role::Student;
$indentClass = $depth > 0 ? 'ml-3 sm:ml-6 pl-3 sm:pl-4 border-l-2 border-indigo-100 mt-3' : '';
@endphp

<div
    id="comment-{{ $comment->id }}"
    class="{{ $indentClass }} space-y-3"
    x-data="{
        replying: false,
        editing: false
    }"
>
    <div class="bg-white rounded-xl border border-gray-200/80 p-4 sm:p-5 shadow-2xs">
        <!-- Comment Header -->
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-2.5">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-50 text-indigo-700 font-bold text-xs uppercase shadow-xs">
                    {{ strtoupper(substr($comment->user?->name ?? 'U', 0, 1)) }}
                </span>
                <span class="font-bold text-xs sm:text-sm text-gray-900">
                    <x-user-name :user="$comment->user" />
                </span>
                <x-badge :variant="$authorRole->badgeColor()" size="sm">
                    {{ $authorRole->label() }}
                </x-badge>
                <span class="text-xs text-gray-400">&bull;</span>
                <time class="text-xs text-gray-400" datetime="{{ $comment->created_at->toISOString() }}" title="{{ $comment->created_at->format('M d, Y H:i') }}">
                    {{ $comment->created_at->diffForHumans() }}
                </time>
                @if ($comment->created_at != $comment->updated_at)
                    <span class="text-[11px] text-gray-400 italic">(edited)</span>
                @endif
            </div>

            <!-- Comment Actions -->
            <div class="flex items-center gap-1.5 text-xs">
                @if (auth()->check() && auth()->user()->isActive())
                    <button
                        type="button"
                        @click="replying = !replying; editing = false"
                        class="inline-flex items-center gap-1 px-2.5 py-1 text-gray-600 hover:text-indigo-600 font-medium rounded-lg hover:bg-indigo-50/50 transition-colors"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 016 6v3" />
                        </svg>
                        <span>Reply</span>
                    </button>
                @endif

                @if ($canUpdateComment)
                    <button
                        type="button"
                        @click="editing = !editing; replying = false"
                        class="inline-flex items-center gap-1 px-2.5 py-1 text-gray-600 hover:text-indigo-600 font-medium rounded-lg hover:bg-gray-100 transition-colors"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        <span>Edit</span>
                    </button>
                @endif

                @if ($canDeleteComment)
                    <form
                        method="POST"
                        action="{{ route('forum.comments.destroy', $comment) }}"
                        onsubmit="return confirm('Are you sure you want to delete this comment?');"
                        class="inline"
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-rose-600 hover:text-rose-700 font-medium rounded-lg hover:bg-rose-50 transition-colors"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            <span>Delete</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Comment Text Body -->
        <div x-show="!editing" class="mt-3 text-xs sm:text-sm text-gray-800 leading-relaxed whitespace-pre-line font-normal">
            {{ $comment->comment }}
        </div>

        <!-- Inline Edit Form -->
        <div x-show="editing" x-cloak class="mt-3 pt-3 border-t border-gray-100">
            <form method="POST" action="{{ route('forum.comments.update', $comment) }}" class="space-y-3">
                @csrf
                @method('PUT')
                <x-textarea
                    name="comment"
                    rows="3"
                    class="w-full text-xs sm:text-sm"
                    required
                >{{ old('comment', $comment->comment) }}</x-textarea>

                <div class="flex items-center justify-end gap-2">
                    <x-button type="button" variant="secondary" size="xs" @click="editing = false">
                        Cancel
                    </x-button>
                    <x-button type="submit" variant="primary" size="xs">
                        Save Changes
                    </x-button>
                </div>
            </form>
        </div>

        <!-- Inline Reply Form -->
        <div x-show="replying" x-cloak class="mt-3 pt-3 border-t border-indigo-50 bg-indigo-50/30 p-3 rounded-xl">
            <form method="POST" action="{{ route('forum.threads.comments.store', $comment->thread) }}" class="space-y-3">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <div class="flex items-center gap-1.5 text-xs text-indigo-700 font-medium">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 016 6v3" />
                    </svg>
                    <span>Replying to <span class="font-bold">{{ $comment->user?->name }}</span></span>
                </div>
                <x-textarea
                    name="comment"
                    rows="2"
                    class="w-full text-xs sm:text-sm bg-white"
                    placeholder="Write your response..."
                    required
                ></x-textarea>

                <div class="flex items-center justify-end gap-2">
                    <x-button type="button" variant="secondary" size="xs" @click="replying = false">
                        Cancel
                    </x-button>
                    <x-button type="submit" variant="primary" size="xs">
                        Send Reply
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Recursive Nested Replies -->
    @if ($comment->replies && $comment->replies->isNotEmpty())
        <div class="space-y-3">
            @foreach ($comment->replies as $nestedReply)
                @include('forum.threads._comment', ['comment' => $nestedReply, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
