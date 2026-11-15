@props([
    'title' => 'No records found',
    'description' => null,
    'icon' => 'book-open',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border-2 border-dashed border-gray-200 bg-white p-10 text-center shadow-xs']) }}>
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 mb-4">
        <x-icon :name="$icon" class="h-7 w-7" />
    </div>

    <h3 class="text-base font-bold text-gray-900">{{ $title }}</h3>

    @if($description)
        <p class="mt-1 text-xs text-gray-500 max-w-sm mx-auto leading-relaxed">
            {{ $description }}
        </p>
    @endif

    @if($slot->isNotEmpty())
        <div class="mt-6 flex justify-center gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
