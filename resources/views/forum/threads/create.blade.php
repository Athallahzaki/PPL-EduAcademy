<x-layouts.app title="Create New Topic">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-xs text-gray-500 gap-2">
            <a href="{{ route('forum.threads.index') }}" class="hover:text-indigo-600 transition-colors">Discussion Forum</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">New Topic</span>
        </nav>

        <!-- Page Header -->
        <x-page-header
            title="Start a New Discussion"
            description="Ask a question, share knowledge, or discuss course material with the EduAcademy community."
        />

        <!-- Form Card -->
        <x-card class="p-6 sm:p-8">
            <form method="POST" action="{{ route('forum.threads.store') }}" class="space-y-6">
                @csrf

                <!-- Subject Category -->
                <div>
                    <x-label for="subject_id" value="Subject Category" required />
                    <x-select id="subject_id" name="subject_id" class="mt-1" required>
                        <option value="">-- Select a subject category --</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>
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
                        placeholder="Example: How do I understand async/await in JavaScript?"
                        value="{{ old('title') }}"
                        required
                        maxlength="255"
                    />
                    <p class="mt-1 text-xs text-gray-500">Write a specific, clear title (at least 5 characters).</p>
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
                        placeholder="Describe your question or discussion topic in detail..."
                        required
                    >{{ old('content') }}</x-textarea>
                    <p class="mt-1 text-xs text-gray-500">Provide full context so others can give helpful answers (at least 10 characters).</p>
                    <x-input-error :messages="$errors->get('content')" class="mt-1" />
                </div>

                <!-- Action Buttons -->
                <x-form-actions :cancel="route('forum.threads.index')" submit="Publish Topic" />
            </form>
        </x-card>
    </div>
</x-layouts.app>
