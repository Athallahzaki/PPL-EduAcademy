@props(['name' => 'q', 'label' => 'Search', 'placeholder' => 'Search...', 'value' => null])

<div {{ $attributes->merge(['class' => '']) }}>
    <x-label :for="$name" :value="$label" />
    <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </div>
        <x-input
            :id="$name"
            type="search"
            :name="$name"
            :value="$value ?? request($name)"
            :placeholder="$placeholder"
            class="pl-9"
        />
    </div>
</div>
