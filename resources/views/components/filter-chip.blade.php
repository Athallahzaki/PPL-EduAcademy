@props(['href', 'active' => false, 'tone' => 'gray', 'count' => null, 'dot' => false])

@php
$tones = [
    'gray' => [
        'active' => 'bg-gray-700 text-white shadow-sm ring-1 ring-gray-700',
        'idle' => 'bg-gray-100 text-gray-700 hover:bg-gray-200 ring-1 ring-gray-500/20',
        'dot' => 'bg-gray-400',
    ],
    'indigo' => [
        'active' => 'bg-indigo-600 text-white shadow-sm ring-1 ring-indigo-600',
        'idle' => 'bg-indigo-50 text-indigo-800 hover:bg-indigo-100 ring-1 ring-indigo-600/20',
        'dot' => 'bg-indigo-500',
    ],
    'emerald' => [
        'active' => 'bg-emerald-600 text-white shadow-sm ring-1 ring-emerald-600',
        'idle' => 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 ring-1 ring-emerald-600/20',
        'dot' => 'bg-emerald-500',
    ],
    'amber' => [
        'active' => 'bg-amber-600 text-white shadow-sm ring-1 ring-amber-600',
        'idle' => 'bg-amber-50 text-amber-800 hover:bg-amber-100 ring-1 ring-amber-600/20',
        'dot' => 'bg-amber-500',
    ],
    'rose' => [
        'active' => 'bg-rose-600 text-white shadow-sm ring-1 ring-rose-600',
        'idle' => 'bg-rose-50 text-rose-800 hover:bg-rose-100 ring-1 ring-rose-600/20',
        'dot' => 'bg-rose-500',
    ],
];

$palette = $tones[$tone] ?? $tones['gray'];
$classes = 'inline-flex shrink-0 items-center gap-2 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors '
    . ($active ? $palette['active'] : $palette['idle']);
@endphp

<a href="{{ $href }}" @if ($active) aria-current="true" @endif {{ $attributes->merge(['class' => $classes]) }}>
    @if ($dot)
        <span class="h-2 w-2 rounded-full {{ $active ? 'bg-white' : $palette['dot'] }}"></span>
    @endif
    <span>{{ $slot }}@if (! is_null($count)) ({{ $count }})@endif</span>
</a>
