@props(['action', 'active' => false, 'resetUrl' => null, 'cols' => 'lg:grid-cols-4'])

<x-card class="p-4 sm:p-5">
    <form method="GET" action="{{ $action }}" class="flex flex-col gap-4 lg:flex-row lg:items-end">
        <div class="grid flex-1 grid-cols-1 items-end gap-3 sm:grid-cols-2 {{ $cols }}">
            {{ $slot }}
        </div>

        <div class="flex shrink-0 items-center justify-end gap-3">
            @if ($active)
                <a href="{{ $resetUrl ?? $action }}" class="text-xs font-medium text-gray-500 underline hover:text-gray-900">
                    Reset filters
                </a>
            @endif
            <x-button type="submit">Filter</x-button>
        </div>
    </form>
    @isset($footer)
        <div class="mt-3 border-t border-gray-100 pt-3 text-xs text-gray-500">
            {{ $footer }}
        </div>
    @endisset
</x-card>
