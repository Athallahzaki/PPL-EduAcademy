<x-layouts.app title="Discussion Forum">
    <div class="space-y-6">
        <!-- Page Header -->
        <x-page-header
            title="Community Discussion Forum"
            description="Structured Q&A and interactive discussion by subject category for students and instructors."
        >
            <x-slot:actions>
                <x-button :href="route('forum.threads.create')" size="md">
<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
</svg>
New Topic
</x-button>
            </x-slot:actions>
        </x-page-header>

        <!-- Subject Chips -->
        <div class="flex flex-wrap items-center gap-2.5">
            <x-filter-chip
                :href="route('forum.threads.index', array_filter(['q' => request('q'), 'sort' => request('sort')]))"
                tone="indigo"
                :active="! request('subject') && ! request('subject_id')"
                :count="$totalThreads"
            >All</x-filter-chip>

            @foreach ($subjects as $subj)
                <x-filter-chip
                    :href="route('forum.threads.index', array_filter(['subject' => $subj->slug, 'q' => request('q'), 'sort' => request('sort')]))"
                    tone="indigo"
                    :active="request('subject') === $subj->slug || (int) request('subject_id') === $subj->id"
                >{{ $subj->name }}</x-filter-chip>
            @endforeach
        </div>

        <!-- Filter Bar -->
        <x-filter-bar
            :action="route('forum.threads.index')"
            :active="request()->hasAny(['q', 'subject', 'subject_id', 'sort'])"
            cols="lg:grid-cols-3"
        >
            @if (request('subject'))
                <input type="hidden" name="subject" value="{{ request('subject') }}">
            @endif
            <x-search-field class="lg:col-span-2" label="Search" placeholder="Search by title, keyword, or author..." />

            <div>
                <x-label for="sort" value="Sort by" />
                <x-select id="sort" name="sort">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Newest</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                    <option value="popular" @selected(request('sort') === 'popular')>Most Discussed</option>
                </x-select>
            </div>
        </x-filter-bar>

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
                            @php($authorRole = $thread->user?->role ?? \App\Enums\Role::Student)
                        <x-badge :variant="$authorRole->badgeColor()" size="sm">{{ $authorRole->label() }}</x-badge>
                        </div>

                        <!-- Stats & Read Link -->
                        <div class="flex items-center gap-4">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                </svg>
                                <span>{{ $thread->comments_count ?? 0 }} {{ Str::plural('reply', $thread->comments_count ?? 0) }}</span>
                            </span>
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
                    <x-button :href="route('forum.threads.create')" size="sm">
Create a Topic Now
</x-button>
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
