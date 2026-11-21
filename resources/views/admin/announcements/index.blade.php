<x-layouts.app title="System Announcements">
    <div class="space-y-6">
        <!-- Page Header -->
        <x-page-header title="System Announcements" description="Create and manage announcements shown on student and instructor dashboards.">
            <x-slot:actions>
                <x-button :href="route('admin.announcements.create')">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Announcement
                </x-button>
            </x-slot:actions>
        </x-page-header>

        <!-- Status Chips -->
        <div class="flex flex-wrap items-center gap-2.5">
            <x-filter-chip
                :href="route('admin.announcements.index', request()->except(['status', 'page']))"
                tone="indigo"
                :active="empty($filters['status'] ?? null)"
                :count="$totalCount"
            >All</x-filter-chip>

            <x-filter-chip
                :href="route('admin.announcements.index', array_merge(request()->except(['status', 'page']), ['status' => 'active']))"
                tone="emerald"
                dot
                :active="($filters['status'] ?? null) === 'active'"
                :count="$activeCount"
            >Active</x-filter-chip>

            <x-filter-chip
                :href="route('admin.announcements.index', array_merge(request()->except(['status', 'page']), ['status' => 'inactive']))"
                tone="gray"
                dot
                :active="($filters['status'] ?? null) === 'inactive'"
                :count="$inactiveCount"
            >Inactive</x-filter-chip>
        </div>

        <!-- Filter Bar -->
        <x-filter-bar
            :action="route('admin.announcements.index')"
            :active="request()->hasAny(['admin_id', 'period', 'status'])"
            cols="lg:grid-cols-3"
        >
            <div>
                <x-label for="admin_id" value="Posted by" />
                <x-select id="admin_id" name="admin_id">
                    <option value="">All administrators</option>
                    @foreach ($admins as $admin)
                        <option value="{{ $admin->id }}" @selected((string) ($filters['admin_id'] ?? '') === (string) $admin->id)>
                            {{ $admin->name }}
                        </option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <x-label for="period" value="Published" />
                <x-select id="period" name="period">
                    <option value="">All dates</option>
                    <option value="7_days" @selected(($filters['period'] ?? '') === '7_days')>Last 7 days</option>
                    <option value="30_days" @selected(($filters['period'] ?? '') === '30_days')>Last 30 days</option>
                    <option value="3_months" @selected(($filters['period'] ?? '') === '3_months')>Last 3 months</option>
                    <option value="6_months" @selected(($filters['period'] ?? '') === '6_months')>Last 6 months</option>
                </x-select>
            </div>

            <div>
                <x-label for="status" value="Status" />
                <x-select id="status" name="status">
                    <option value="">All statuses</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                </x-select>
            </div>

            <x-slot:footer>
                Announcements older than six months are archived and hidden from student and instructor dashboards.
            </x-slot:footer>
        </x-filter-bar>

        <x-table>
            <x-slot:headers>
                <th scope="col" class="px-6 py-3.5">Announcement</th>
                <th scope="col" class="px-6 py-3.5">Created By</th>
                <th scope="col" class="px-6 py-3.5">Published</th>
                <th scope="col" class="px-6 py-3.5">Status</th>
                <th scope="col" class="px-6 py-3.5 text-right">Actions</th>
            </x-slot:headers>

            @forelse ($announcements as $announcement)
                <tr class="hover:bg-gray-50/75 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900">{{ $announcement->title }}</p>
                                <p class="mt-0.5 max-w-lg truncate text-xs text-gray-500">{{ $announcement->content }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $announcement->admin?->name ?? 'Former administrator' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                        {{ $announcement->published_at?->format('M d, Y') ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col items-start gap-1.5">
                            @if ($announcement->isArchived())
                                <x-badge variant="amber">Archived</x-badge>
                            @endif
                            @if ($announcement->is_active)
                                <x-badge variant="active">Active</x-badge>
                            @else
                                <x-badge variant="gray">Inactive</x-badge>
                            @endif
                        </div>
                    </td>
                    <x-table.actions>
                        <x-row-action :href="route('admin.announcements.edit', $announcement)">Edit</x-row-action>
                        <x-row-action
                            :action="route('admin.announcements.destroy', $announcement)"
                            method="DELETE"
                            variant="danger"
                            confirm="Delete this announcement? This action cannot be undone."
                        >Delete</x-row-action>
                    </x-table.actions>
                </tr>
            @empty
                <x-table.empty colspan="5" message="No announcements have been created yet." />
            @endforelse
        </x-table>

        <div class="mt-4">
            {{ $announcements->links() }}
        </div>
    </div>
</x-layouts.app>
