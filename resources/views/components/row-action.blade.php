@props([
    'href' => null,
    'action' => null,
    'method' => 'POST',
    'confirm' => null,
    'variant' => 'default',
    'fields' => [],
])

@php
$base = 'inline-flex items-center justify-center rounded-lg border px-2.5 py-1 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1';

$variants = [
    'default' => 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
    'primary' => 'border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100',
    'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100',
    'danger' => 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100',
    'muted' => 'border-gray-200 bg-gray-100 text-gray-700 hover:bg-gray-200',
];

$classes = $base . ' ' . ($variants[$variant] ?? $variants['default']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@elseif ($action)
    <form
        method="POST"
        action="{{ $action }}"
        class="inline-block"
        @if ($confirm) onsubmit="return confirm({{ \Illuminate\Support\Js::from($confirm) }});" @endif
    >
        @csrf
        @if (strtoupper($method) !== 'POST')
            @method($method)
        @endif
        @foreach ($fields as $fieldName => $fieldValue)
            <input type="hidden" name="{{ $fieldName }}" value="{{ $fieldValue }}">
        @endforeach
        <button type="submit" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
    </form>
@endif
