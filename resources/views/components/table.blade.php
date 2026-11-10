@props(['headers' => null])

<div class="overflow-hidden bg-white shadow-sm rounded-xl border border-gray-200/80">
    <div class="overflow-x-auto">
        <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200 text-left text-sm']) }}>
            @if($headers)
                <thead class="bg-gray-50/75 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        {{ $headers }}
                    </tr>
                </thead>
            @endif
            <tbody class="divide-y divide-gray-100 bg-white">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>

