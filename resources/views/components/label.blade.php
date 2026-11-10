@props(['value' => null, 'required' => false])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-red-500 font-bold">*</span>
    @endif
</label>
