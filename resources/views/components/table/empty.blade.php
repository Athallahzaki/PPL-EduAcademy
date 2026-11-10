@props(['colspan' => 1, 'message' => 'No records found.', 'description' => 'Try adjusting your search or filters.'])

<tr>
    <td colspan="{{ $colspan }}" class="px-6 py-12 text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 text-gray-400">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
            </svg>
        </div>
        <p class="mt-3 text-sm font-medium text-gray-900">{{ $message }}</p>
        @if($description)
            <p class="mt-1 text-xs text-gray-500">{{ $description }}</p>
        @endif
    </td>
</tr>
