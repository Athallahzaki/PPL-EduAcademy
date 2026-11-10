@props(['user' => null, 'name' => null, 'suspended' => false])

@php
$userName = $user ? $user->name : $name;
$isSuspended = $user ? (method_exists($user, 'isSuspended') ? $user->isSuspended() : ($user->status?->value ?? $user->status) === 'suspended') : $suspended;
@endphp

<span class="inline-flex items-center gap-2">
    <span class="font-medium text-gray-900">{{ $userName }}</span>
    @if($isSuspended)
        <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/20">
            Suspended
        </span>
    @endif
</span>

