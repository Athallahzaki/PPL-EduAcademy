<x-layouts.app :title="$thread->title">
    @php
    $currentUser = auth()->user();
    $canUpdateThread = $currentUser && ($currentUser->isAdmin() || $currentUser->id === $thread->user_id);
    $canDeleteThread = $currentUser && ($currentUser->isAdmin() || $currentUser->id === $thread->user_id);
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center text-xs text-gray-500 gap-2 flex-wrap">
            <a href="{{ route('forum.threads.index') }}" class="hover:text-indigo-600 transition-colors">Discussion Forum</a>
            <span>&rsaquo;</span>
            @if ($thread->subject)
                <a href="{{ route('forum.threads.index', ['subject' => $thread->subject->slug]) }}" class="hover:text-indigo-600 transition-colors">
                    {{ $thread->subject->name }}
                </a>
                <span>&rsaquo;</span>
            @endif
            <span class="text-gray-900 font-medium truncate max-w-sm">{{ $thread->title }}</span>
        </nav>

        <!-- Main Thread Card -->
        <article class="bg-white rounded-2xl border border-gray-200/80 p-6 sm:p-8 shadow-sm">
            <!-- Header: Category & Actions -->
            <div class="flex items-center justify-between gap-4 flex-wrap pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <x-badge variant="indigo">
                        {{ $thread->subject?->name ?? 'General' }}
                    </x-badge>
                    <span class="text-xs text-gray-400">&bull;</span>
                    <time class="text-xs text-gray-500" datetime="{{ $thread->created_at->toISOString() }}" title="{{ $thread->created_at->format('M d, Y H:i') }}">
                        Posted {{ $thread->created_at->diffForHumans() }}
                    </time>
                </div>

                <!-- Action Buttons (Edit / Delete) -->
                <div class="flex items-center gap-2">
                    @if ($canUpdateThread)
                        <x-row-action :href="route('forum.threads.edit', $thread)">Edit Topic</x-row-action>
                    @endif

                    @if ($canDeleteThread)
                        <x-row-action
                            :action="route('forum.threads.destroy', $thread)"
                            method="DELETE"
                            variant="danger"
                            confirm="Are you sure you want to permanently delete this topic?"
                        >Delete</x-row-action>
                    @endif
                </div>
            </div>

            <!-- Thread Title -->
            <h1 class="mt-4 text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight leading-snug">
                {{ $thread->title }}
            </h1>

            <!-- Author info -->
            <div class="mt-4 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm uppercase shadow-xs">
                    {{ strtoupper(substr($thread->user?->name ?? 'U', 0, 1)) }}
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-sm text-gray-900">
                            <x-user-name :user="$thread->user" />
                        </span>
                        @php($authorRole = $thread->user?->role ?? \App\Enums\Role::Student)
                        <x-badge :variant="$authorRole->badgeColor()" size="sm">{{ $authorRole->label() }}</x-badge>
                    </div>
                    <div class="text-xs text-gray-500">
                        Topic Author
                    </div>
                </div>
            </div>

            <!-- Thread Content Body -->
            <div class="mt-6 pt-6 border-t border-gray-100 text-gray-800 text-sm leading-relaxed whitespace-pre-line font-normal">
                {{ $thread->content }}
            </div>

            <!-- Footer actions / navigation -->
            <div class="mt-8 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <a href="{{ route('forum.threads.index') }}" class="inline-flex items-center gap-1.5 font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Forum
                </a>
            </div>
        </article>

        <!-- Comments & Responses Section (PBI-15) -->
        <section class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                    <span>Comments &amp; Responses</span>
                    <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                        {{ $thread->comments_count ?? $thread->comments->count() }}
                    </span>
                </h2>
            </div>

            <!-- Root Comment Form -->
            @if (auth()->check() && auth()->user()->isActive())
                <x-card class="p-5 sm:p-6">
                    <form method="POST" action="{{ route('forum.threads.comments.store', $thread) }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-label for="root-comment" value="Leave a response" />
                            <x-textarea
                                id="root-comment"
                                name="comment"
                                rows="3"
                                class="mt-1 w-full text-sm"
                                placeholder="Share your perspective or ask a question regarding this topic..."
                                required
                            >{{ old('comment') }}</x-textarea>
                            <x-input-error :messages="$errors->get('comment')" class="mt-1" />
                        </div>

                        <div class="flex justify-end">
                            <x-button type="submit" variant="primary" size="sm">
                                Post Comment
                            </x-button>
                        </div>
                    </form>
                </x-card>
            @endif

            <!-- Comments Tree -->
            @if ($thread->rootComments && $thread->rootComments->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($thread->rootComments as $rootComment)
                        @include('forum.threads._comment', ['comment' => $rootComment, 'depth' => 0])
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center">
                    <p class="text-sm font-medium text-gray-600">No responses yet on this topic.</p>
                    <p class="text-xs text-gray-400 mt-1">Be the first to share your thoughts or answer!</p>
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
