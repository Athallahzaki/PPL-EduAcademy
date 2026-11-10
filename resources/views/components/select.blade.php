@props(['disabled' => false, 'error' => null])

@php
$classes = 'block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-colors py-2 px-3 border bg-white';
if ($error) {
    $classes .= ' border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500';
}
@endphp

<select {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</select>

