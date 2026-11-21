<x-layouts.app :title="$scholarship->title">
    <div class="mx-auto max-w-5xl space-y-6">
        <!-- Header & Navigation -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.scholarships.index') }}"
                    class="rounded-xl border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-xs"
                    title="Back to scholarship list"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $scholarship->title }}</h1>
                        @if ($scholarship->trashed())
                            <x-badge variant="suspended">Deleted</x-badge>
                        @else
                            <x-badge :variant="$scholarship->status->badgeColor()" size="lg">
                                {{ $scholarship->status->label() }}
                            </x-badge>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Published by <span class="font-medium text-gray-700">{{ $scholarship->admin?->name ?? 'System Admin' }}</span>
                        on {{ $scholarship->created_at->format('M d, Y H:i') }}
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2.5">
                @if (! $scholarship->trashed())
                    <a
                        href="{{ route('admin.scholarships.edit', $scholarship) }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-xs hover:bg-gray-50 transition-colors"
                    >
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Edit
                    </a>

                    <form
                        method="POST"
                        action="{{ route('admin.scholarships.destroy', $scholarship) }}"
                        onsubmit="return confirm('Are you sure you want to delete this scholarship?');"
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100 transition-colors"
                        >
                            <svg class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            Delete
                        </button>
                    </form>
                @else
                    <form
                        method="POST"
                        action="{{ route('admin.scholarships.restore', $scholarship->id) }}"
                    >
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors"
                        >
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            <span>Restore Scholarship</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Key Parameter Stats -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Recipient Quota</p>
                    <span class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600">
                        <x-icon name="users" class="h-5 w-5" />
                    </span>
                </div>
                <p class="mt-3 text-3xl font-extrabold tracking-tight text-gray-900">{{ number_format($scholarship->quota) }}</p>
                <p class="mt-1 text-xs text-gray-500">Scholarship seat capacity</p>
            </div>

            <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Deadline</p>
                    <span class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600">
                        <x-icon name="calendar" class="h-5 w-5" />
                    </span>
                </div>
                <p class="mt-3 text-xl font-bold tracking-tight text-gray-900">{{ $scholarship->deadline->format('M d, Y') }}</p>
                <div class="mt-1 text-xs">
                    @if ($scholarship->isExpired())
                        <span class="font-medium text-rose-600">Registration has closed</span>
                    @elseif ($scholarship->isOpen())
                        <span class="font-medium text-emerald-600">Open ({{ $scholarship->daysRemaining() }} days left)</span>
                    @else
                        <span class="font-medium text-amber-600">Registration has not started</span>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Publication Status</p>
                    <span class="rounded-xl bg-sky-50 p-2.5 text-sky-600">
                        <x-icon name="clipboard-check" class="h-5 w-5" />
                    </span>
                </div>
                <div class="mt-3">
                    <x-badge :variant="$scholarship->status->badgeColor()" size="lg">
                        {{ $scholarship->status->label() }}
                    </x-badge>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    {{ $scholarship->status === App\Enums\ScholarshipStatus::Published ? 'Visible to all prospective applicants' : 'Not shown in the public catalog' }}
                </p>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Col: Description & Requirements -->
            <div class="space-y-6 lg:col-span-2">
                @if ($scholarship->description)
                    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-xs">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 border-b border-gray-100 pb-3">
                            Program Description
                        </h2>
                        <div class="mt-4 prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $scholarship->description }}
                        </div>
                    </div>
                @endif

                <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-xs">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900">
                            Registration Terms & Requirements
                        </h2>
                        <span class="text-xs text-indigo-600 font-semibold bg-indigo-50 px-2.5 py-1 rounded-lg">Must be met by students</span>
                    </div>
                    <div class="mt-4 prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-line bg-slate-50/70 p-4 rounded-xl border border-slate-100">
                        {{ $scholarship->requirements }}
                    </div>
                </div>
            </div>

            <!-- Right Col: Metadata & Sprint 3 Ready Info -->
            <div class="space-y-6">
                <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-xs space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 border-b border-gray-100 pb-3">
                        Schedule
                    </h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-500">Registration Starts:</span>
                            <span class="font-medium text-gray-900">{{ $scholarship->start_date ? $scholarship->start_date->format('M d, Y') : 'Soon' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-500">Deadline:</span>
                            <span class="font-medium text-gray-900">{{ $scholarship->deadline->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-500">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{ $scholarship->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-indigo-100 bg-indigo-50/50 p-5 text-xs text-indigo-950 space-y-2">
                    <div class="flex items-center gap-2 font-bold text-indigo-900">
                        <x-icon name="academic-cap" class="h-4 w-4 text-indigo-600" />
                        <span>Sprint 3 Integration (PBI-02)</span>
                    </div>
                    <p class="leading-relaxed text-indigo-800">
                        This scholarship is ready to connect with <strong>PBI-02: Scholarship Registration & Selection</strong>, where students upload requirement files and admins validate the result.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
