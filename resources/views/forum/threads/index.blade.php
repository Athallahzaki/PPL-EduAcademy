<x-layouts.app title="Discussion Forum">
    <div class="space-y-6">
        <!-- Page Header -->
        <x-page-header
            title="Community Discussion Forum"
            description="Structured Q&A and interactive discussion by subject category for students and instructors."
        >
            <x-slot:actions>
                <a
                    href="{{ route('forum.threads.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New Topic
                </a>
            </x-slot:actions>
        </x-page-header>

        <!-- Flash Message Alerts -->
        @if (session('status'))
            <x-alert type="success" :message="session('status')" />
        @endif
        @if (session('warning'))
            <x-alert type="warning" :message="session('warning')" />
        @endif

        <!-- Filter & Search Card -->
        <x-card class="p-4 sm:p-5">
            <form method="GET" action="{{ route('forum.threads.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-12 items-end">
                    <!-- Search input -->
                    <div class="md:col-span-6 lg:col-span-7">
                        <x-label for="q" value="Search Topics" />
                        <div class="relative mt-1">
                            <input
                                type="text"
                                id="q"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Search by title, keyword, or author..."
                                class="w-full rounded-xl border border-gray-300 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                            />
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Sort select -->
                    <div class="md:col-span-3 lg:col-span-3">
                        <x-label for="sort" value="Sort by" />
                        <select
                            id="sort"
                            name="sort"
                            onchange="this.form.submit()"
                            class="mt-1 w-full rounded-xl border border-gray-300 py-2.5 px-3 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                        >
                            <option value="latest" @selected(request('sort', 'latest') === 'latest')>Newest</option>
                            <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                        </select>
                    </div>

                    <!-- Filter action buttons -->
                    <div class="md:col-span-3 lg:col-span-2 flex items-center gap-2">
                        <x-button type="submit" variant="primary" class="w-full justify-center">
                            Filter
                        </x-button>
                        @if (request()->hasAny(['q', 'subject', 'subject_id', 'sort']))
                            <a
                                href="{{ route('forum.threads.index') }}"
                                class="rounded-xl border border-gray-300 px-3 py-2.5 text-xs font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors shrink-0"
                                title="Reset filters"
                            >
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Subject Category Pills -->
                <div class="pt-2 border-t border-gray-100">
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                        <span class="font-semibold text-gray-500 mr-1 shrink-0">Category:</span>
                        <a
                            href="{{ route('forum.threads.index', array_filter(['q' => request('q'), 'sort' => request('sort')])) }}"
                            class="rounded-full px-3 py-1 font-medium transition-colors shrink-0 {{ !request('subject') && !request('subject_id') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            All ({{ $totalThreads }})
                        </a>
                        @foreach ($subjects as $subj)
                            @php
                                $isSubjActive = (request('subject') === $subj->slug) || ((int) request('subject_id') === $subj->id);
                            @endphp
                            <a
                                href="{{ route('forum.threads.index', array_filter(['subject' => $subj->slug, 'q' => request('q'), 'sort' => request('sort')])) }}"
                                class="rounded-full px-3 py-1 font-medium transition-colors shrink-0 {{ $isSubjActive ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            >
                                {{ $subj->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </form>
        </x-card>

        <!-- Threads Listing -->
        <div class="space-y-4">
            @forelse ($threads as $thread)
                <div class="bg-white rounded-2xl border border-gray-200/80 p-5 sm:p-6 shadow-sm hover:border-indigo-200 hover:shadow-md transition-all">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-2 flex-1 min-w-0">
                            <!-- Category Badge & Date -->
                            <div class="flex items-center gap-2.5 flex-wrap text-xs text-gray-500">
                                <x-badge variant="indigo">
                                    {{ $thread->subject?->name ?? 'General' }}
                                </x-badge>
                                <span>&bull;</span>
                                <time datetime="{{ $thread->created_at->toISOString() }}" title="{{ $thread->created_at->format('M d, Y H:i') }}">
                                    {{ $thread->created_at->diffForHumans() }}
                                </time>
                            </div>

                            <!-- Title -->
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 hover:text-indigo-600 transition-colors">
                                <a href="{{ route('forum.threads.show', $thread) }}" class="block truncate sm:whitespace-normal">
                                    {{ $thread->title }}
                                </a>
                            </h3>

                            <!-- Excerpt -->
                            <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed">
                                {{ Str::limit(strip_tags($thread->content), 180) }}
                            </p>
                        </div>
                    </div>

                    <!-- Bottom Bar: Author info, Comments count, Actions -->
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3 text-xs">
                        <!-- Author -->
                        <div class="flex items-center gap-2">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs uppercase">
                                {{ strtoupper(substr($thread->user?->name ?? 'U', 0, 1)) }}
                            </span>
                            <span class="font-medium text-gray-900">
                                <x-user-name :user="$thread->user" />
                            </span>
                            @if ($thread->user?->isInstructor())
                                <x-badge variant="purple" size="sm">Instructor</x-badge>
                            @elseif ($thread->user?->isAdmin())
                                <x-badge variant="amber" size="sm">Admin</x-badge>
                            @else
                                <x-badge variant="gray" size="sm">Student</x-badge>
                            @endif
                        </div>

                        <!-- Stats & Read Link -->
                        <div class="flex items-center gap-4">
                            <a
                                href="{{ route('forum.threads.show', $thread) }}"
                                class="inline-flex items-center gap-1 font-semibold text-indigo-600 hover:text-indigo-700"
                            >
                                View Topic &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <x-empty-state
                    title="No discussion topics yet"
                    description="There are no topics in this category or search yet. Be the first to start a discussion!"
                    icon="chat-alt-2"
                >
                    <a
                        href="{{ route('forum.threads.create') }}"
                        class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors shadow-sm"
                    >
                        Create a Topic Now
                    </a>
                </x-empty-state>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($threads->hasPages())
            <div class="mt-6">
                {{ $threads->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
