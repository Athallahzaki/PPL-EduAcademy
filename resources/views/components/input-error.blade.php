@props(['messages' => null, 'for' => null])

@php
if ($for && empty($messages)) {
    $messages = $errors->get($for);
}
@endphp

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-xs text-red-600 mt-1 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif

