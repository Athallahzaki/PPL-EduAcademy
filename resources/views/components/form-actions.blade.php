@props(['cancel', 'submit' => 'Save Changes', 'cancelLabel' => 'Cancel'])

<div {{ $attributes->merge(['class' => 'flex flex-col-reverse gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:items-center sm:justify-end']) }}>
    <x-button :href="$cancel" variant="secondary">{{ $cancelLabel }}</x-button>
    <x-button type="submit">{{ $submit }}</x-button>
</div>
