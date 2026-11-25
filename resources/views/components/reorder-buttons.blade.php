@props([
    'upAction' => '',
    'downAction' => '',
    'isFirst' => false,
    'isLast' => false,
])

<div class="inline-flex items-center gap-1">
    {{-- Move Up Button --}}
    @if($isFirst)
        <span class="p-1 rounded text-gray-300 cursor-not-allowed" title="Already at top">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
            </svg>
        </span>
    @else
        <form action="{{ $upAction }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="direction" value="up">
            <button type="submit" class="p-1 rounded text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Move Up">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
            </button>
        </form>
    @endif

    {{-- Move Down Button --}}
    @if($isLast)
        <span class="p-1 rounded text-gray-300 cursor-not-allowed" title="Already at bottom">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </span>
    @else
        <form action="{{ $downAction }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="direction" value="down">
            <button type="submit" class="p-1 rounded text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Move Down">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </form>
    @endif
</div>
