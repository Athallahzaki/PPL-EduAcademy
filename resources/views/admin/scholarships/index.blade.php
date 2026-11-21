<x-layouts.app title="Scholarship Listings">
    <div class="space-y-6">
        <x-page-header title="Scholarship Listings" description="Manage and publish scholarship programs, eligibility requirements, and recipient quotas.">
            <x-slot:actions>
                <a
                    href="{{ route('admin.scholarships.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Publish Scholarship
                </a>
            </x-slot:actions>
        </x-page-header>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card
                title="Total Programs"
                :value="$totalCount"
                icon="academic-cap"
                variant="indigo"
                description="All scholarship programs"
            />
            <x-stat-card
                title="Published"
                :value="$publishedCount"
                icon="clipboard-check"
                variant="emerald"
                description="Live programs"
            />
            <x-stat-card
                title="Total Quota"
                :value="number_format($totalQuota)"
                icon="users"
                variant="sky"
                description="Active recipient quota"
            />
            <x-stat-card
                title="Closed"
                :value="$closedCount"
                icon="calendar"
                variant="amber"
                description="Finished or closed programs"
            />
        </div>

        <!-- Filters Card -->
        <x-card class="p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.scholarships.index') }}" class="grid grid-cols-1 items-end gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <x-label for="q" value="Search Scholarships" />
                    <x-input
                        id="q"
                        name="q"
                        type="text"
                        placeholder="Title or requirements..."
                        value="{{ request('q') }}"
                    />
                </div>

                <div>
                    <x-label for="status" value="Publication Status" />
                    <x-select id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="published" @selected(request('status') === 'published')>Published</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        <option value="closed" @selected(request('status') === 'closed')>Closed</option>
                    </x-select>
                </div>

                <div>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer h-10 pt-2">
                        <input
                            type="checkbox"
                            name="trashed"
                            value="1"
                            @checked(request()->boolean('trashed'))
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <span>Show deleted</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3">
                    @if (request()->hasAny(['q', 'status', 'trashed']))
                        <a href="{{ route('admin.scholarships.index') }}" class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 hover:text-gray-900 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Reset filters</span>
                        </a>
                    @endif
                    <x-button type="submit" class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                        </svg>
                        <span>Filter</span>
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Scholarship Table -->
        <x-table>
            <x-slot:headers>
                <th scope="col" class="px-6 py-3.5">Scholarship</th>
                <th scope="col" class="px-6 py-3.5">Quota</th>
                <th scope="col" class="px-6 py-3.5">Deadline</th>
                <th scope="col" class="px-6 py-3.5">Status</th>
                <th scope="col" class="px-6 py-3.5">Published By</th>
                <th scope="col" class="px-6 py-3.5 text-right">Actions</th>
            </x-slot:headers>

            @forelse ($scholarships as $scholarship)
                <tr class="hover:bg-gray-50/75 transition-colors">
                    <td class="px-6 py-4">
                        <div class="min-w-0">
                            <a href="{{ route('admin.scholarships.show', $scholarship) }}" class="font-semibold text-gray-900 hover:text-indigo-600 transition-colors">
                                {{ $scholarship->title }}
                            </a>
                            @if ($scholarship->description)
                                <p class="mt-0.5 line-clamp-1 text-xs text-gray-500">{{ $scholarship->description }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                            <x-icon name="users" class="h-3.5 w-3.5 text-indigo-600" />
                            {{ number_format($scholarship->quota) }} seats
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                        <div>
                            <span class="font-medium text-gray-900">{{ $scholarship->deadline->format('M d, Y') }}</span>
                            @if ($scholarship->start_date)
                                <div class="text-[11px] text-gray-400">Starts: {{ $scholarship->start_date->format('M d, Y') }}</div>
                            @endif
                            @if ($scholarship->isExpired())
                                <span class="mt-0.5 inline-block text-[11px] font-medium text-rose-600">Ended</span>
                            @elseif ($scholarship->isOpen())
                                <span class="mt-0.5 inline-block text-[11px] font-medium text-emerald-600">{{ $scholarship->daysRemaining() }} days left</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($scholarship->trashed())
                            <x-badge variant="suspended">Deleted</x-badge>
                        @else
                            <x-badge :variant="$scholarship->status->badgeColor()">
                                {{ $scholarship->status->label() }}
                            </x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                        {{ $scholarship->admin?->name ?? 'System Admin' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                        <div class="flex items-center justify-end gap-1.5">
                            <!-- View Button -->
                            <a
                                href="{{ route('admin.scholarships.show', $scholarship) }}"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50/70 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 shadow-2xs hover:bg-indigo-100 hover:text-indigo-800 transition-colors"
                                title="View scholarship details"
                            >
                                <svg class="h-3.5 w-3.5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>View</span>
                            </a>

                            @if (! $scholarship->trashed())
                                <!-- Edit Button -->
                                <a
                                    href="{{ route('admin.scholarships.edit', $scholarship) }}"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-gray-700 shadow-2xs hover:bg-gray-50 hover:text-gray-900 transition-colors"
                                    title="Edit scholarship"
                                >
                                    <svg class="h-3.5 w-3.5 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                    <span>Edit</span>
                                </a>

                                <!-- Delete Button -->
                                <form
                                    method="POST"
                                    action="{{ route('admin.scholarships.destroy', $scholarship) }}"
                                    onsubmit="return confirm('Delete this scholarship? It can be restored later by showing deleted items.');"
                                    class="inline-block"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50/70 px-2.5 py-1.5 text-xs font-semibold text-rose-700 shadow-2xs hover:bg-rose-100 hover:text-rose-800 transition-colors"
                                        title="Delete scholarship"
                                    >
                                        <svg class="h-3.5 w-3.5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            @else
                                <!-- Restore Button -->
                                <form
                                    method="POST"
                                    action="{{ route('admin.scholarships.restore', $scholarship->id) }}"
                                    class="inline-block"
                                >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 shadow-2xs hover:bg-emerald-100 hover:text-emerald-800 transition-colors"
                                        title="Restore scholarship"
                                    >
                                        <svg class="h-3.5 w-3.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                        <span>Restore</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <x-table.empty colspan="6" message="No scholarships have been published yet." />
            @endforelse
        </x-table>

        <div class="mt-4">
            {{ $scholarships->links() }}
        </div>
    </div>
</x-layouts.app>
