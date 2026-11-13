@props(['announcement'])

<section
    x-data="{ visible: true }"
    x-show="visible"
    class="rounded-xl bg-indigo-600 p-4 text-white shadow-sm"
    aria-labelledby="announcement-title-{{ $announcement->id }}"
>
    <div class="flex items-start gap-4">
        @if ($announcement->image_url)
            <img
                src="{{ $announcement->image_url }}"
                alt=""
                class="h-16 w-16 shrink-0 rounded-lg object-cover"
                loading="lazy"
            >
        @endif
        <div class="min-w-0 flex-1">
            <h2 id="announcement-title-{{ $announcement->id }}" class="font-semibold">
                {{ $announcement->title }}
            </h2>
            <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-indigo-100">{{ $announcement->content }}</p>
        </div>
        <button
            type="button"
            @click="visible = false"
            class="rounded-lg p-1 text-indigo-100 transition hover:bg-indigo-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
            aria-label="Dismiss announcement"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</section>
