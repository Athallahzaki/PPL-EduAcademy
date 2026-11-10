<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 Server Error — {{ config('app.name', 'EduAcademy') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-slate-50 flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-2xl p-8 border border-gray-100 shadow-sm text-center">
        <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 mb-6">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">500</h1>
        <h2 class="mt-2 text-lg font-semibold text-gray-800">Server Error</h2>
        <p class="mt-2 text-sm text-gray-500">
            An unexpected error occurred while processing your request. Our team has been notified.
        </p>

        <div class="mt-8 flex justify-center">
            <a
                href="/"
                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
            >
                Return to Home
            </a>
        </div>
    </div>
</body>
</html>

