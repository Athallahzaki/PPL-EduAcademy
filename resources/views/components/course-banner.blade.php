@props(['course', 'class' => ''])

@php
$gradients = [
    'from-indigo-600 to-purple-700',
    'from-blue-600 to-cyan-700',
    'from-emerald-600 to-teal-800',
    'from-amber-600 to-orange-700',
    'from-rose-600 to-pink-700',
    'from-violet-600 to-indigo-800',
];
$gradientIndex = abs(crc32($course->title . ($course->id ?? ''))) % count($gradients);
$gradient = $gradients[$gradientIndex];

// Course title initials
$words = explode(' ', trim($course->title));
$initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
@endphp

<div {{ $attributes->merge(['class' => 'relative aspect-video w-full overflow-hidden bg-slate-100 ' . $class]) }}>
    @if ($course->banner_url)
        <img
            src="{{ $course->banner_url }}"
            alt="{{ $course->title }}"
            loading="lazy"
            class="h-full w-full object-cover transition-transform duration-300 hover:scale-105"
        />
    @else
        <!-- Deterministic Gradient Placeholder (§3.6 / §8.3) -->
        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br {{ $gradient }} text-white p-4 text-center">
            <div class="flex flex-col items-center justify-center gap-1.5 opacity-90">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 text-lg font-extrabold backdrop-blur-xs tracking-wider shadow-inner">
                    {{ $initials ?: 'EA' }}
                </span>
                <span class="text-xs font-medium text-white/80 line-clamp-1 max-w-[80%]">
                    {{ $course->subject?->name ?? 'Course' }}
                </span>
            </div>
        </div>
    @endif
</div>
