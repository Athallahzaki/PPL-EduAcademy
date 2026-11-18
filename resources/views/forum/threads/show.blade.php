<x-layouts.app :title="$thread->title">
    @php
    $currentUser = auth()->user();
    $canUpdateThread = $currentUser && ($currentUser->isAdmin() || $currentUser->id === $thread->user_id);
    $canDeleteThread = $currentUser && ($currentUser->isAdmin() || $currentUser->id === $thread->user_id);
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center text-xs text-gray-500 gap-2 flex-wrap">
            <a href="{{ route('forum.threads.index') }}" class="hover:text-indigo-600 transition-colors">Forum Diskusi</a>
            <span>&rsaquo;</span>
            @if ($thread->subject)
                <a href="{{ route('forum.threads.index', ['subject' => $thread->subject->slug]) }}" class="hover:text-indigo-600 transition-colors">
                    {{ $thread->subject->name }}
                </a>
                <span>&rsaquo;</span>
            @endif
            <span class="text-gray-900 font-medium truncate max-w-sm">{{ $thread->title }}</span>
        </nav>

        <!-- Flash Messages -->
        @if (session('status'))
            <x-alert type="success" :message="session('status')" />
        @endif
        @if (session('warning'))
            <x-alert type="warning" :message="session('warning')" />
        @endif
        @if ($errors->any())
            <x-alert type="danger" message="Terjadi kesalahan pengisian form. Silakan periksa kembali." />
        @endif

        <!-- Main Thread Card -->
        <article class="bg-white rounded-2xl border border-gray-200/80 p-6 sm:p-8 shadow-sm">
            <!-- Header: Category & Actions -->
            <div class="flex items-center justify-between gap-4 flex-wrap pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <x-badge variant="indigo">
                        {{ $thread->subject?->name ?? 'Umum' }}
                    </x-badge>
                    <span class="text-xs text-gray-400">&bull;</span>
                    <time class="text-xs text-gray-500" datetime="{{ $thread->created_at->toISOString() }}" title="{{ $thread->created_at->translatedFormat('d F Y, H:i') }}">
                        Diposting {{ $thread->created_at->diffForHumans() }}
                    </time>
                </div>

                <!-- Action Buttons (Edit / Delete) -->
                <div class="flex items-center gap-2">
                    @if ($canUpdateThread)
                        <a
                            href="{{ route('forum.threads.edit', $thread) }}"
                            class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            <svg class="h-3.5 w-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Edit Topik
                        </a>
                    @endif

                    @if ($canDeleteThread)
                        <form
                            method="POST"
                            action="{{ route('forum.threads.destroy', $thread) }}"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus topik diskusi ini secara permanen?');"
                            class="inline"
                        >
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-white px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors"
                            >
                                <svg class="h-3.5 w-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                Hapus
                            </button>
                        </form>
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
                        @if ($thread->user?->isInstructor())
                            <x-badge variant="purple" size="sm">Instruktur</x-badge>
                        @elseif ($thread->user?->isAdmin())
                            <x-badge variant="amber" size="sm">Admin</x-badge>
                        @else
                            <x-badge variant="gray" size="sm">Siswa</x-badge>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500">
                        Penulis Topik Diskusi
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
                    Kembali ke Daftar Forum
                </a>
            </div>
        </article>
    </div>
</x-layouts.app>
