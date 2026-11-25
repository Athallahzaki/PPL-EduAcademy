@props(['chapter'])

<div class="space-y-6">
    @if($chapter->type->value === 'video')
        <x-video-embed :video="$chapter->video" />

        @if($chapter->body)
            <div class="mt-6 pt-6 border-t border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Lesson Notes
                </h3>
                <x-markdown :content="$chapter->body" />
            </div>
        @endif
    @else
        <x-markdown :content="$chapter->body" />
    @endif
</div>
