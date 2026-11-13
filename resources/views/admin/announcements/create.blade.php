<x-layouts.app title="Create Announcement">
    <div class="mx-auto max-w-3xl space-y-6">
        <x-page-header title="Create Announcement" description="Write an announcement for the student and instructor dashboards." />

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
            <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="title" class="mb-1.5 block text-sm font-medium text-slate-700">Title</label>
                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title') }}"
                        maxlength="255"
                        required
                        autofocus
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    >
                    @error('title') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="content" class="mb-1.5 block text-sm font-medium text-slate-700">Content</label>
                    <textarea
                        id="content"
                        name="content"
                        rows="7"
                        maxlength="65535"
                        required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    >{{ old('content') }}</textarea>
                    @error('content') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="image_url" class="mb-1.5 block text-sm font-medium text-slate-700">Image URL <span class="font-normal text-slate-500">(optional)</span></label>
                    <input
                        id="image_url"
                        name="image_url"
                        type="url"
                        value="{{ old('image_url') }}"
                        maxlength="255"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    >
                    @error('image_url') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <input type="hidden" name="is_active" value="0">
                    <label for="is_active" class="inline-flex cursor-pointer items-center gap-2 text-sm font-medium text-slate-700">
                        <input
                            id="is_active"
                            name="is_active"
                            type="checkbox"
                            value="1"
                            @checked(old('is_active', '1') === '1' || old('is_active') === 1)
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        Active
                    </label>
                    @error('is_active') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
                    <a href="{{ route('admin.announcements.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">Create Announcement</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
