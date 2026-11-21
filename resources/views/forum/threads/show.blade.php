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
    </div>
</x-layouts.app>
