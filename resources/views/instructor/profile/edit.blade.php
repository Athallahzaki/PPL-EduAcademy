<x-layouts.app title="Edit Instructor Profile">
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <a href="{{ route('instructor.profile.show') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">&larr; Back to Profile</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900">Edit Profilee & Specialization</h1>
            <p class="mt-1 text-sm text-gray-500">Complete your professional details so prospective students can learn about your experience and expertise.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <p class="font-semibold">Please review the data you entered:</p>
                <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            @method('PUT')

            <div>
                <label for="bio" class="mb-2 block text-sm font-semibold text-gray-700">Bio / About Me</label>
                <textarea id="bio" name="bio" rows="5" maxlength="1000" required placeholder="Describe your background, teaching experience, and approach to learning..." class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('bio', $profile?->bio) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Maximum 1,000 characters.</p>
            </div>

            <div>
                <label for="expertise" class="mb-2 block text-sm font-semibold text-gray-700">Expertise & Specialization</label>
                <input id="expertise" name="expertise" type="text" maxlength="255" required value="{{ old('expertise', $profile?->expertise) }}" placeholder="Example: UI/UX Design, Laravel, Database, Data Analytics" class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500">Separate multiple skills with commas.</p>
            </div>

            <div>
                <label for="portfolio_url" class="mb-2 block text-sm font-semibold text-gray-700">Portfolio URL</label>
                <input id="portfolio_url" name="portfolio_url" type="url" maxlength="255" value="{{ old('portfolio_url', $profile?->portfolio_url) }}" placeholder="https://portofolio-anda.com" class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500">Enter a link to your website, GitHub, Behance, or other portfolio.</p>
            </div>

            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-5">
                <label for="cv" class="block text-sm font-semibold text-gray-700">CV (Curriculum Vitae)</label>
                @if ($profile?->cv_path)
                    <p class="mt-2 text-xs text-emerald-700">A CV is already saved. Upload a new file to replace it.</p>
                @else
                    <p class="mt-2 text-xs text-gray-500">No CV yet. Please upload your CV as a PDF.</p>
                @endif
                <input id="cv" name="cv" type="file" accept="application/pdf,.pdf" class="mt-3 block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-2 text-xs text-gray-500">PDF only, maximum 2 MB. Leave empty to keep your current CV.</p>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('instructor.profile.show') }}" class="inline-flex justify-center rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Save Changes</button>
            </div>
        </form>
    </div>
</x-layouts.app>
