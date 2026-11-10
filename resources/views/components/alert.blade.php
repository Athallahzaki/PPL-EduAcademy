@if (session('temporary_password'))
    <div x-data="{ copied: false }" class="mb-6 rounded-xl border border-amber-300 bg-amber-50 p-5 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="rounded-lg bg-amber-100 p-2 text-amber-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-amber-900">Temporary Password Generated</h3>
                <p class="mt-1 text-xs text-amber-800">
                    This password is shown <strong>only once</strong> and is not stored in plain text. Please copy and deliver it securely to the user.
                </p>
                <div class="mt-3 flex items-center gap-3">
                    <code class="rounded-lg bg-white px-3 py-1.5 font-mono text-sm font-bold text-gray-900 border border-amber-200 select-all tracking-wider">
                        {{ session('temporary_password') }}
                    </code>
                    <button
                        type="button"
                        @click="navigator.clipboard.writeText('{{ session('temporary_password') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white shadow-xs hover:bg-amber-700 transition-colors"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.849A1.996 1.996 0 0014.5 3.5h-5a2 2 0 00-2 2v1m10.666-.651A1.996 1.996 0 0118.5 6.5v13a2 2 0 01-2 2h-9a2 2 0 01-2-2v-1m10.666-14.651a2.003 2.003 0 00-2-1.349H9.5a2 2 0 00-2 1.349m12 0v1m-12-1v1m0 0a2 2 0 002 2h6a2 2 0 002-2" />
                        </svg>
                        <span x-text="copied ? 'Copied!' : 'Copy Password'">Copy Password</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@if (session('success'))
    <div x-data="{ show: true }" x-show="show" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="rounded-lg bg-emerald-100 p-1.5 text-emerald-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1 pt-0.5">
                <p class="text-sm font-medium text-emerald-900">{{ session('success') }}</p>
            </div>
            <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-md">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif

@if (session('status'))
    <div x-data="{ show: true }" x-show="show" class="mb-6 rounded-xl border border-indigo-200 bg-indigo-50 p-4 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="rounded-lg bg-indigo-100 p-1.5 text-indigo-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            </div>
            <div class="flex-1 pt-0.5">
                <p class="text-sm font-medium text-indigo-900">{{ session('status') }}</p>
            </div>
            <button type="button" @click="show = false" class="text-indigo-500 hover:text-indigo-700 p-1 rounded-md">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-show="show" class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="rounded-lg bg-rose-100 p-1.5 text-rose-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
            </div>
            <div class="flex-1 pt-0.5">
                <p class="text-sm font-medium text-rose-900">{{ session('error') }}</p>
            </div>
            <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700 p-1 rounded-md">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif

@if (session('info'))
    <div x-data="{ show: true }" x-show="show" class="mb-6 rounded-xl border border-sky-200 bg-sky-50 p-4 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="rounded-lg bg-sky-100 p-1.5 text-sky-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            </div>
            <div class="flex-1 pt-0.5">
                <p class="text-sm font-medium text-sky-900">{{ session('info') }}</p>
            </div>
            <button type="button" @click="show = false" class="text-sky-500 hover:text-sky-700 p-1 rounded-md">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif

