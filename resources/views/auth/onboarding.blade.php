<x-layouts.guest title="Complete Your Profile">
    <div class="mb-5 text-center">
        <p class="text-xs text-gray-500">
            Welcome to EduAcademy, <strong>{{ $user->name }}</strong>! Please complete the onboarding information below to get started.
        </p>
    </div>

    <form method="POST" action="{{ route('onboarding.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        @if ($user->isStudent())
            <!-- Student Onboarding: Education Level -->
            <div>
                <x-label for="education_level" value="Education Level" required />
                <x-select id="education_level" name="education_level" required :error="$errors->has('education_level')">
                    <option value="" disabled {{ old('education_level') ? '' : 'selected' }}>Select your current education level</option>
                    <option value="elementary" {{ old('education_level') === 'elementary' ? 'selected' : '' }}>Elementary School (SD)</option>
                    <option value="junior_high" {{ old('education_level') === 'junior_high' ? 'selected' : '' }}>Junior High School (SMP)</option>
                    <option value="senior_high" {{ old('education_level') === 'senior_high' ? 'selected' : '' }}>Senior High School / Vocational (SMA/SMK)</option>
                    <option value="university" {{ old('education_level', 'university') === 'university' ? 'selected' : '' }}>University / Higher Education (D3/S1/S2/S3)</option>
                    <option value="other" {{ old('education_level') === 'other' ? 'selected' : '' }}>Other / General Public</option>
                </x-select>
                <x-input-error :messages="$errors->get('education_level')" />
            </div>
        @elseif ($user->isInstructor())
            <!-- Instructor Onboarding: Profile info & CV -->
            <div>
                <x-label for="expertise" value="Primary Area of Expertise" required />
                <x-input
                    id="expertise"
                    type="text"
                    name="expertise"
                    :value="old('expertise')"
                    required
                    placeholder="e.g. Fullstack Web Development, Data Science"
                    :error="$errors->has('expertise')"
                />
                <x-input-error :messages="$errors->get('expertise')" />
            </div>

            <div>
                <x-label for="bio" value="Professional Bio (max 1000 characters)" required />
                <x-textarea
                    id="bio"
                    name="bio"
                    rows="3"
                    required
                    placeholder="Tell students about your professional background and teaching focus..."
                    :error="$errors->has('bio')"
                >{{ old('bio') }}</x-textarea>
                <x-input-error :messages="$errors->get('bio')" />
            </div>

            <div>
                <x-label for="portfolio_url" value="Portfolio / Website URL (optional)" />
                <x-input
                    id="portfolio_url"
                    type="url"
                    name="portfolio_url"
                    :value="old('portfolio_url')"
                    placeholder="https://yourportfolio.com"
                    :error="$errors->has('portfolio_url')"
                />
                <x-input-error :messages="$errors->get('portfolio_url')" />
            </div>

            <div>
                <x-label for="cv" value="Curriculum Vitae (PDF only, max 2MB)" required />
                <input
                    id="cv"
                    type="file"
                    name="cv"
                    accept=".pdf,application/pdf"
                    required
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-gray-300 rounded-lg p-1"
                />
                <p class="mt-1 text-xs text-gray-500">Your CV will be securely archived on private storage for verification.</p>
                <x-input-error :messages="$errors->get('cv')" />
            </div>
        @endif

        <div class="pt-2">
            <x-button class="w-full">
                Complete Onboarding & Enter EduAcademy
            </x-button>
        </div>
    </form>

    <div class="mt-6 border-t border-gray-100 pt-4 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 hover:underline">
                Sign out and complete later
            </button>
        </form>
    </div>
</x-layouts.guest>

