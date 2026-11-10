<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 Forbidden — {{ config('app.name', 'EduAcademy') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-slate-50 flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-2xl p-8 border border-gray-100 shadow-sm text-center">
        <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 mb-6">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">403</h1>
        <h2 class="mt-2 text-lg font-semibold text-gray-800">Access Forbidden</h2>
        <p class="mt-2 text-sm text-gray-500">
            You do not have permission to access this resource or page with your current role.
        </p>

        <div class="mt-8 flex justify-center">
            @auth
                @php
                    $dashboardRoute = 'login';
                    if (method_exists(auth()->user(), 'dashboardRoute')) {
                        $dashboardRoute = auth()->user()->dashboardRoute();
                    } elseif (auth()->user()->role) {
                        $role = auth()->user()->role?->value ?? auth()->user()->role;
                        $dashboardRoute = "{$role}.dashboard";
                    }
                @endphp
                <a
                    href="{{ \Illuminate\Support\Facades\Route::has($dashboardRoute) ? route($dashboardRoute) : '/' }}"
                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                >
                    Return to Dashboard
                </a>
            @else
                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                >
                    Sign in to your account
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
