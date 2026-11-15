@props(['course', 'role' => 'instructor'])

@php
$tabs = config("course_tabs.{$role}", []);
@endphp

<div class="border-b border-gray-200">
    <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
        @foreach ($tabs as $tab)
            @if (isset($tab['route']) && Route::has($tab['route']))
                @php
                    $isActive = isset($tab['active']) && request()->routeIs($tab['active']);
                @endphp
                <a
                    href="{{ route($tab['route'], $course) }}"
                    class="whitespace-nowrap border-b-2 py-3 px-1 text-sm font-semibold transition-all {{ $isActive ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}"
                    {{ $isActive ? 'aria-current="page"' : '' }}
                >
                    {{ $tab['label'] }}
                </a>
            @endif
        @endforeach
    </nav>
</div>
