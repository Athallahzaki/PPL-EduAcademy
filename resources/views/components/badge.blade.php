@props(['variant' => 'gray', 'size' => 'md'])

@php
$variants = [
    'pending' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20',
    'amber' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20',
    'active' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20',
    'green' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20',
    'success' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20',
    'suspended' => 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20',
    'danger' => 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20',
    'red' => 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20',
    'rejected' => 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-500/20',
    'gray' => 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-500/20',
    'indigo' => 'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-600/20',
    'blue' => 'bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-600/20',
    'purple' => 'bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20',
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-1 text-xs',
    'lg' => 'px-3 py-1 text-sm',
];

$classes = 'inline-flex items-center font-medium rounded-full ' . ($variants[$variant] ?? $variants['gray']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>

