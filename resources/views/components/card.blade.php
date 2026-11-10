@props(['header' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden']) }}>
    @if($header)
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            {{ $header }}
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $footer }}
        </div>
    @endif
</div>
