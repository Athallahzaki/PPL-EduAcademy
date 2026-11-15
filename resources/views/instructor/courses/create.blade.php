<x-layouts.app title="Create Class">
    <div class="max-w-3xl mx-auto space-y-6">
        <x-page-header title="Create Class" description="Fill out the initial details to draft a new course. You can upload curriculum content and publish it when ready." />

        <x-card>
            <form method="POST" action="{{ route('instructor.courses.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Title -->
                <div>
                    <x-label for="title" value="Class Title" required />
                    <x-input
                        id="title"
                        type="text"
                        name="title"
                        :value="old('title')"
                        required
                        autofocus
                        placeholder="e.g. Modern Full-Stack Web Development with Laravel & Vue"
                        :error="$errors->has('title')"
                    />
                    <x-input-error :messages="$errors->get('title')" />
                </div>

                <!-- Grid: Subject & Level -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Subject -->
                    <div>
                        <x-label for="subject_id" value="Subject Category" required />
                        <x-select id="subject_id" name="subject_id" required :error="$errors->has('subject_id')">
                            <option value="" disabled {{ old('subject_id') ? '' : 'selected' }}>Select a subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('subject_id')" />
                    </div>

                    <!-- Level -->
                    <div>
                        <x-label for="level" value="Target Difficulty Level" required />
                        <x-select id="level" name="level" required :error="$errors->has('level')">
                            <option value="beginner" {{ old('level', 'beginner') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ old('level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ old('level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </x-select>
                        <x-input-error :messages="$errors->get('level')" />
                    </div>
                </div>

                <!-- Description with live character counter (§6.2) -->
                <div x-data="{ charCount: {{ strlen(old('description', '')) }} }">
                    <div class="flex items-center justify-between">
                        <x-label for="description" value="Course Description (min 20 characters)" required />
                        <span class="text-[11px] text-gray-500 font-mono" x-text="`${charCount} / 5000 characters`"></span>
                    </div>
                    <x-textarea
                        id="description"
                        name="description"
                        rows="5"
                        required
                        @input="charCount = $el.value.length"
                        placeholder="Provide a comprehensive summary of what students will learn, course prerequisites, and module outlines..."
                        :error="$errors->has('description')"
                    >{{ old('description') }}</x-textarea>
                    <x-input-error :messages="$errors->get('description')" />
                </div>

                <!-- Banner Upload -->
                <x-file-input
                    name="banner"
                    id="banner"
                    label="Course Banner (Optional)"
                    :error="$errors->first('banner')"
                />

                <!-- Submit / Cancel -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a
                        href="{{ route('instructor.courses.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </a>
                    <x-button type="submit">
                        Create Class (Draft)
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
