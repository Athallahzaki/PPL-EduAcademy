@props(['type' => 'submit', 'variant' => 'primary', 'size' => 'md'])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg';

$sizes = [
    'xs' => 'px-2.5 py-1 text-xs',
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-base',
];

$variants = [
    'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 shadow-sm',
    'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-indigo-500 shadow-sm',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 shadow-sm',
    'warning' => 'bg-amber-600 text-white hover:bg-amber-700 focus:ring-amber-500 shadow-sm',
    'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500 shadow-sm',
    'ghost' => 'text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:ring-gray-400',
];

$classes = $baseClasses . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
