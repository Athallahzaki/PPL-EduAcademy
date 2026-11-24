<x-layouts.app title="Scholarship Listings">
    <div class="space-y-6">
        <!-- Page Header -->
        <x-page-header title="Scholarship Listings" description="Manage and publish scholarship programs, eligibility requirements, and recipient quotas.">
            <x-slot:actions>
                <x-button :href="route('admin.scholarships.create')">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Publish Scholarship
                </x-button>
            </x-slot:actions>
        </x-page-header>

        <!-- Status Chips -->
        <div class="flex flex-wrap items-center gap-2.5">
            <x-filter-chip
                :href="route('admin.scholarships.index', array_filter(['q' => request('q')]))"
                tone="indigo"
                :active="! request('status') && ! request()->boolean('trashed')"
                :count="$totalCount"
            >All</x-filter-chip>

            <x-filter-chip
                :href="route('admin.scholarships.index', array_filter(['status' => 'published', 'q' => request('q')]))"
                tone="emerald"
                dot
                :active="request('status') === 'published'"
                :count="$publishedCount"
            >Published</x-filter-chip>

            <x-filter-chip
                :href="route('admin.scholarships.index', array_filter(['status' => 'draft', 'q' => request('q')]))"
                tone="gray"
                dot
                :active="request('status') === 'draft'"
                :count="$draftCount"
            >Draft</x-filter-chip>

            <x-filter-chip
                :href="route('admin.scholarships.index', array_filter(['status' => 'closed', 'q' => request('q')]))"
                tone="rose"
                dot
                :active="request('status') === 'closed'"
                :count="$closedCount"
            >Closed</x-filter-chip>

            <span class="ml-auto text-xs text-gray-500">
                Open quota across published programs: <span class="font-semibold text-gray-700">{{ number_format($totalQuota) }}</span> seats
            </span>
        </div>

        <!-- Filter Bar -->
        <x-filter-bar
            :action="route('admin.scholarships.index')"
            :active="request()->hasAny(['q', 'status', 'trashed'])"
            cols="lg:grid-cols-4"
        >
            <x-search-field class="lg:col-span-2" label="Search" placeholder="Search by title or requirements..." />

            <div>
                <x-label for="status" value="Status" />
                <x-select id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="closed" @selected(request('status') === 'closed')>Closed</option>
                </x-select>
            </div>

            <x-filter-toggle name="trashed" label="Show deleted" :checked="request()->boolean('trashed')" />
        </x-filter-bar>

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
                <tr class="hover:bg-gray-50/75 transition-colors {{ $scholarship->trashed() ? 'bg-gray-50/50 opacity-75' : '' }}">
                    <!-- Title & summary -->
                    <td class="px-6 py-4">
                        <div class="min-w-0">
                            <a href="{{ route('admin.scholarships.show', $scholarship) }}" class="font-semibold text-gray-900 hover:text-indigo-600 transition-colors">
                                {{ $scholarship->title }}
                            </a>
                            @if ($scholarship->description)
                                <p class="mt-0.5 line-clamp-1 max-w-md text-xs text-gray-500">{{ $scholarship->description }}</p>
                            @endif
                        </div>
                    </td>

                    <!-- Quota -->
                    <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-gray-900">
                        {{ number_format($scholarship->quota) }} seats
                    </td>

                    <!-- Deadline -->
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                        <div class="font-medium text-gray-900">{{ $scholarship->deadline->format('M d, Y') }}</div>
                        @if ($scholarship->start_date)
                            <div class="mt-0.5 text-[11px] text-gray-400">Starts: {{ $scholarship->start_date->format('M d, Y') }}</div>
                        @endif
                        @if ($scholarship->isExpired())
                            <div class="mt-0.5 text-[11px] font-medium text-rose-600">Ended</div>
                        @elseif ($scholarship->isOpen())
                            <div class="mt-0.5 text-[11px] font-medium text-emerald-600">{{ $scholarship->daysRemaining() }} days left</div>
                        @endif
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($scholarship->trashed())
                            <x-badge variant="red">Deleted</x-badge>
                        @else
                            <x-badge :variant="$scholarship->status->badgeColor()">
                                {{ $scholarship->status->label() }}
                            </x-badge>
                        @endif
                    </td>

                    <!-- Published by -->
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                        {{ $scholarship->admin?->name ?? 'System Admin' }}
                    </td>

                    <!-- Row Actions -->
                    <x-table.actions>
                        @if ($scholarship->trashed())
                            <x-row-action :action="route('admin.scholarships.restore', $scholarship->id)" variant="success">Restore</x-row-action>
                        @else
                            <x-row-action :href="route('admin.scholarships.show', $scholarship)" variant="primary">View</x-row-action>
                            <x-row-action :href="route('admin.scholarships.edit', $scholarship)">Edit</x-row-action>
                            <x-row-action
                                :action="route('admin.scholarships.destroy', $scholarship)"
                                method="DELETE"
                                variant="danger"
                                confirm="Delete this scholarship? It can be restored later by showing deleted items."
                            >Delete</x-row-action>
                        @endif
                    </x-table.actions>
                </tr>
            @empty
                <x-table.empty colspan="6" message="No scholarships found." description="Publish a scholarship or adjust your search and filters." />
            @endforelse
        </x-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $scholarships->links() }}
        </div>
    </div>
</x-layouts.app>
