<x-layouts.app title="Edit Class: {{ $course->title }}">
    <div class="max-w-3xl mx-auto space-y-6">
        <x-page-header title="Edit Class Details" description="Modify title, description, subject category, or banner image.">
            <x-slot:actions>
                <a
                    href="{{ route('instructor.courses.show', $course) }}"
                    class="rounded-lg border border-gray-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    &larr; Back to Class Hub
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-card>
            <form method="POST" action="{{ route('instructor.courses.update', $course) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div>
                    <x-label for="title" value="Class Title" required />
                    <x-input
                        id="title"
                        type="text"
                        name="title"
                        :value="old('title', $course->title)"
                        required
                        autofocus
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
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $course->subject_id) == $subject->id ? 'selected' : '' }}>
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
                            <option value="beginner" {{ old('level', $course->level->value) === 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ old('level', $course->level->value) === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ old('level', $course->level->value) === 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </x-select>
                        <x-input-error :messages="$errors->get('level')" />
                    </div>
                </div>

                <!-- Description with live character counter -->
                <div x-data="{ charCount: {{ strlen(old('description', $course->description)) }} }">
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
                        :error="$errors->has('description')"
                    >{{ old('description', $course->description) }}</x-textarea>
                    <x-input-error :messages="$errors->get('description')" />
                </div>

                <!-- Banner Upload -->
                <x-file-input
                    name="banner"
                    id="banner"
                    label="Course Banner"
                    :currentUrl="$course->banner_url"
                    :error="$errors->first('banner')"
                />

                <!-- Submit / Cancel -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a
                        href="{{ route('instructor.courses.show', $course) }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </a>
                    <x-button type="submit">
                        Save Changes
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>

