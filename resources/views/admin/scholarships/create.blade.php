<x-layouts.app title="Publish Scholarship">
    <div class="mx-auto max-w-4xl space-y-6">
        <x-page-header title="Publish Scholarship" description="Fill in the program details, eligibility requirements, and recipient quota." />

        <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-xs sm:p-8">
            <form method="POST" action="{{ route('admin.scholarships.store') }}" class="space-y-6">
                @csrf

                <!-- Basic Information -->
                <div class="space-y-4">
                    <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-2">Program Information</h2>

                    <div>
                        <x-label for="title" value="Scholarship Title" required />
                        <x-input
                            id="title"
                            name="title"
                            type="text"
                            placeholder="Example: Digital Talent AI Scholarship 2026"
                            value="{{ old('title') }}"
                            required
                            autofocus
                        />
                        <x-input-error :messages="$errors->get('title')" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-label for="quota" value="Recipient Quota (People)" required />
                            <x-input
                                id="quota"
                                name="quota"
                                type="number"
                                min="1"
                                placeholder="Contoh: 50"
                                value="{{ old('quota') }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('quota')" />
                        </div>

                        <div>
                            <x-label for="status" value="Publication Status" required />
                            <x-select id="status" name="status" required>
                                <option value="published" @selected(old('status', 'published') === 'published')>Published (Live immediately)</option>
                                <option value="draft" @selected(old('status') === 'draft')>Draft (Saved, not visible)</option>
                                <option value="closed" @selected(old('status') === 'closed')>Closed</option>
                            </x-select>
                            <x-input-error :messages="$errors->get('status')" />
                        </div>
                    </div>

                    <div>
                        <x-label for="description" value="Summary / Short Description" />
                        <x-textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Describe the organizer, program scope, or scholarship benefits..."
                        >{{ old('description') }}</x-textarea>
                        <x-input-error :messages="$errors->get('description')" />
                    </div>
                </div>

                <!-- Schedule & Deadlines -->
                <div class="space-y-4 pt-2">
                    <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-2">Schedule & Registration Period</h2>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-label for="start_date" value="Registration Start Date (Optional)" />
                            <x-input
                                id="start_date"
                                name="start_date"
                                type="date"
                                value="{{ old('start_date', date('Y-m-d')) }}"
                            />
                            <x-input-error :messages="$errors->get('start_date')" />
                        </div>

                        <div>
                            <x-label for="deadline" value="Registration Deadline" required />
                            <x-input
                                id="deadline"
                                name="deadline"
                                type="date"
                                value="{{ old('deadline') }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('deadline')" />
                        </div>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="space-y-4 pt-2">
                    <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-2">Requirements & Documents</h2>

                    <div>
                        <x-label for="requirements" value="Eligibility Requirements & Required Documents" required />
                        <p class="mb-2 text-xs text-gray-500">
                            State the applicant requirements clearly (e.g. education level, minimum GPA, portfolio or certificate documents to upload).
                        </p>
                        <x-textarea
                            id="requirements"
                            name="requirements"
                            rows="6"
                            placeholder="1. Active student in Indonesia&#10;2. Attach a recommendation letter or student ID card&#10;3. Attach a CV and relevant portfolio&#10;4. Motivation essay of at least 500 words"
                            required
                        >{{ old('requirements') }}</x-textarea>
                        <x-input-error :messages="$errors->get('requirements')" />
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <a
                        href="{{ route('admin.scholarships.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-2xs transition hover:bg-gray-50 hover:text-gray-900"
                    >
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Cancel</span>
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Publish Scholarship</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
