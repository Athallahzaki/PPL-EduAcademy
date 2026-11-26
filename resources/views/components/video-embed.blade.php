@props(['video' => null])

@if($video && $video->isEmbeddable())
    <div class="aspect-video w-full rounded-xl overflow-hidden shadow-sm border border-gray-200 bg-black">
        <iframe
            src="{{ $video->embedUrl }}"
            class="w-full h-full"
            loading="lazy"
            allowfullscreen
            referrerpolicy="strict-origin-when-cross-origin">
        </iframe>
    </div>
@elseif($video)
    <div class="p-6 bg-slate-50 border border-slate-200 rounded-xl text-center space-y-3">
        <div class="inline-flex p-3 rounded-full bg-indigo-50 text-indigo-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </div>
        <h4 class="text-base font-semibold text-gray-900">External Video Lesson</h4>
        <p class="text-sm text-gray-600 max-w-md mx-auto">
            This video resource is hosted on an external platform. Click the button below to open it in a new tab.
        </p>
        <div>
            <a href="{{ $video->originalUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                <span>Open Video</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        </div>
    </div>
@endif
