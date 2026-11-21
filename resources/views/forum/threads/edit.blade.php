<x-layouts.app title="Edit Topic">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-xs text-gray-500 gap-2">
            <a href="{{ route('forum.threads.index') }}" class="hover:text-indigo-600 transition-colors">Discussion Forum</a>
            <span>&rsaquo;</span>
            <a href="{{ route('forum.threads.show', $thread) }}" class="hover:text-indigo-600 transition-colors truncate max-w-xs">{{ $thread->title }}</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">Edit Topic</span>
        </nav>

        <!-- Page Header -->
        <x-page-header
            title="Edit Topic"
            description="Update the details or content of this forum topic."
        />

        <!-- Form Card -->
        <x-card class="p-6 sm:p-8">
            <form method="POST" action="{{ route('forum.threads.update', $thread) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Subject Category -->
                <div>
                    <x-label for="subject_id" value="Subject Category" required />
                    <x-select id="subject_id" name="subject_id" class="mt-1" required>
                        <option value="">-- Select a subject category --</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id', $thread->subject_id) == $subject->id)>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </x-select>
                    <x-input-error :messages="$errors->get('subject_id')" class="mt-1" />
                </div>

                <!-- Title -->
                <div>
                    <x-label for="title" value="Topic Title" required />
                    <x-input
                        id="title"
                        name="title"
                        type="text"
                        class="mt-1 w-full"
                        value="{{ old('title', $thread->title) }}"
                        required
                        maxlength="255"
                    />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <!-- Content -->
                <div>
                    <x-label for="content" value="Discussion / Question" required />
                    <x-textarea
                        id="content"
                        name="content"
                        rows="8"
                        class="mt-1 w-full"
                        required
                    >{{ old('content', $thread->content) }}</x-textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-1" />
                </div>

                <!-- Action Buttons -->
                <x-form-actions :cancel="route('forum.threads.show', $thread)" submit="Save Changes" />
            </form>
        </x-card>
    </div>
</x-layouts.app>
