@props([
    'title',
    'value',
    'icon' => 'chart-bar',
    'description' => null,
    'variant' => 'indigo',
    'link' => null,
])

@php
$iconBg = match($variant) {
    'emerald', 'green' => 'bg-emerald-50 text-emerald-600',
    'amber' => 'bg-amber-50 text-amber-600',
    'rose', 'red' => 'bg-rose-50 text-rose-600',
    'sky', 'blue' => 'bg-sky-50 text-sky-600',
    default => 'bg-indigo-50 text-indigo-600',
};
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200/80 bg-white p-5 shadow-xs']) }}>
    <div class="flex items-center justify-between">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $title }}</p>
        <span class="rounded-xl p-2.5 {{ $iconBg }}">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>
    </div>

    <p class="mt-3 text-3xl font-extrabold tracking-tight text-gray-900">{{ $value }}</p>

    @if($description || $link)
        <div class="mt-2 text-xs flex items-center justify-between">
            @if($description)
                <span class="text-gray-500">{{ $description }}</span>
            @endif
            @if($link)
                <a href="{{ $link }}" class="font-semibold text-indigo-600 hover:text-indigo-700 ml-auto">
                    View &rarr;
                </a>
            @endif
        </div>
    @endif
</div>

