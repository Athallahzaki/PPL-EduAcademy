<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 Page Expired — {{ config('app.name', 'EduAcademy') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-slate-50 flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-2xl p-8 border border-gray-100 shadow-sm text-center">
        <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 mb-6">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">419</h1>
        <h2 class="mt-2 text-lg font-semibold text-gray-800">Page Expired</h2>
        <p class="mt-2 text-sm text-gray-500">
            Your security token has expired due to inactivity. Please refresh and try again.
        </p>

        <div class="mt-8 flex justify-center gap-3">
            <button
                type="button"
                onclick="window.location.reload()"
                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
            >
                Refresh Page
            </button>
            <a
                href="/"
                class="inline-flex items-center justify-center rounded-lg bg-white border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
            >
                Home
            </a>
        </div>
    </div>
</body>
</html>

