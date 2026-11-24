@props(['name', 'label', 'checked' => false])

<label {{ $attributes->merge(['class' => 'inline-flex h-10 cursor-pointer items-center gap-2 text-sm text-gray-600']) }}>
    <input
        type="checkbox"
        name="{{ $name }}"
        value="1"
        @checked($checked)
        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
    >
    <span>{{ $label }}</span>
</label>
