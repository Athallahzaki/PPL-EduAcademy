@props(['user' => null, 'name' => null, 'suspended' => false])

@php
$userName = $user ? $user->name : $name;
$isSuspended = $user ? (method_exists($user, 'isSuspended') ? $user->isSuspended() : ($user->status?->value ?? $user->status) === 'suspended') : $suspended;
@endphp

<span class="inline-flex items-center gap-2">
    <span class="font-medium text-gray-900">{{ $userName }}</span>
    @if($isSuspended)
        <x-badge variant="red" size="sm">Suspended</x-badge>
    @endif
</span>

