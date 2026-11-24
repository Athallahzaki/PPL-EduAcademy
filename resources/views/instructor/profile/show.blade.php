<x-layouts.app title="Instructor Profile">
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-4 rounded-2xl bg-gradient-to-r from-indigo-700 to-indigo-900 p-6 text-white sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-indigo-200">INSTRUCTOR PROFILE</p>
                <h1 class="mt-1 text-2xl font-bold">{{ auth()->user()->name }}</h1>
                <p class="mt-2 max-w-2xl text-sm text-indigo-100">Manage your professional information, expertise, CV, and portfolio.</p>
            </div>
            <a href="{{ route('instructor.profile.edit') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">Edit Profile</a>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <section class="space-y-5 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm md:col-span-2">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">About Me</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-gray-600">{{ $profile?->bio ?: 'No profile description yet.' }}</p>
                </div>
                <div class="border-t border-gray-100 pt-5">
                    <h2 class="text-lg font-bold text-gray-900">Expertise & Specialization</h2>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @forelse (preg_split('/[,;]+/', $profile?->expertise ?? '', -1, PREG_SPLIT_NO_EMPTY) as $skill)
                            <x-badge variant="indigo" size="lg">{{ trim($skill) }}</x-badge>
                        @empty
                            <p class="text-sm text-gray-500">No expertise added yet.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <aside class="space-y-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="font-bold text-gray-900">CV Document</h2>
                    <p class="mt-2 text-sm text-gray-500">Your professional CV in PDF format.</p>
                    @if ($profile?->cv_path)
                        <a href="{{ route('instructor.profile.cv') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-xl border border-indigo-200 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">Download CV</a>
                    @else
                        <p class="mt-4 rounded-lg bg-amber-50 p-3 text-xs text-amber-800">CV has not been uploaded.</p>
                    @endif
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="font-bold text-gray-900">Portfolio</h2>
                    @if ($profile?->portfolio_url)
                        <a href="{{ $profile->portfolio_url }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex break-all text-sm font-medium text-indigo-600 underline hover:text-indigo-800">{{ $profile->portfolio_url }}</a>
                    @else
                        <p class="mt-2 text-sm text-gray-500">No portfolio link yet.</p>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>
