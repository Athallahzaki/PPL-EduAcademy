<x-layouts.app title="System Announcements">
    <div class="space-y-6">
        <x-page-header title="System Announcements" description="Create and manage announcements shown on student and instructor dashboards.">
            <x-slot:actions>
                <a
                    href="{{ route('admin.announcements.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Announcement
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-card class="p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.announcements.index') }}" class="grid grid-cols-1 items-end gap-3 sm:grid-cols-2 lg:grid-cols-4">
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

                <div class="flex items-center justify-end gap-3">
                    @if (request()->hasAny(['admin_id', 'period', 'status']))
                        <a href="{{ route('admin.announcements.index') }}" class="text-xs font-medium text-gray-500 underline hover:text-gray-900">
                            Reset filters
                        </a>
                    @endif
                    <x-button type="submit">Filter</x-button>
                </div>
            </form>
            <p class="mt-3 text-xs text-gray-500">
                Announcements older than six months are archived and hidden from student and instructor dashboards.
            </p>
        </x-card>

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
                        @if ($announcement->isArchived())
                            <span class="mb-1 inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Archived</span>
                            <br>
                        @endif
                        @if ($announcement->is_active)
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Active</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                        <div class="flex items-center justify-end gap-3">
                            <a
                                href="{{ route('admin.announcements.edit', $announcement) }}"
                                class="text-gray-600 hover:text-gray-900 font-semibold"
                            >
                                Edit
                            </a>
                            <form
                                method="POST"
                                action="{{ route('admin.announcements.destroy', $announcement) }}"
                                onsubmit="return confirm('Delete this announcement? This action cannot be undone.');"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-900 font-semibold">Delete</button>
                            </form>
                        </div>
                    </td>
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
