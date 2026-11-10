@props(['title' => null, 'header' => null])

@php
$user = auth()->user();
$roleKey = $user?->role?->value ?? (is_string($user?->role) ? $user->role : 'student');
$navItems = config("navigation.roles.{$roleKey}", []);
$helpRoute = config('navigation.help_route', 'help');

// Initials avatar
$initials = '';
if ($user?->name) {
    $words = explode(' ', trim($user->name));
    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
}
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' — ' . config('app.name', 'EduAcademy') : config('app.name', 'EduAcademy') }}</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-slate-50" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex flex-col lg:flex-row">
        <!-- Mobile off-canvas backdrop -->
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/60 z-40 lg:hidden"
            @click="sidebarOpen = false"
            style="display: none;"
        ></div>

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200/80 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0"
        >
            <!-- Logo Header -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-gray-100">
                <a href="/" class="flex items-center gap-2.5 font-bold text-xl tracking-tight text-indigo-600">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5z" />
                        </svg>
                    </span>
                    <span>Edu<span class="text-gray-900">Academy</span></span>
                </a>
                <button
                    type="button"
                    class="lg:hidden text-gray-400 hover:text-gray-600 p-1 rounded-lg"
                    @click="sidebarOpen = false"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
                @foreach ($navItems as $item)
                    @if (isset($item['route']) && Route::has($item['route']))
                        @php
                            $isActive = isset($item['active']) && request()->routeIs($item['active']);
                        @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                        >
                            <x-icon
                                :name="$item['icon'] ?? 'home'"
                                class="h-5 w-5 shrink-0 transition-colors {{ $isActive ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                            />
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            <!-- Bottom Section (Help & Logout) -->
            <div class="p-4 border-t border-gray-100 space-y-1">
                @if (Route::has($helpRoute))
                    <a
                        href="{{ route($helpRoute) }}"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-xl transition-colors"
                    >
                        <x-icon name="question-mark-circle" class="h-5 w-5 text-gray-400" />
                        <span>Help & Support</span>
                    </a>
                @endif

                @if(Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-xl transition-colors"
                        >
                            <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            <span>Sign out</span>
                        </button>
                    </form>
                @endif
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="h-16 bg-white border-b border-gray-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="lg:hidden -ml-2 p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100"
                        @click="sidebarOpen = true"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    @if($title)
                        <h2 class="text-lg font-bold text-gray-900 hidden sm:block">{{ $title }}</h2>
                    @endif
                </div>

                <!-- User profile & status -->
                @if($user)
                    <div class="flex items-center gap-3" x-data="{ userMenuOpen: false }">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-semibold text-gray-900">
                                <x-user-name :user="$user" />
                            </div>
                            <div class="text-xs text-gray-500">
                                <span class="capitalize">{{ $user->role?->label() ?? (is_string($user->role) ? ucfirst($user->role) : '') }}</span>
                            </div>
                        </div>

                        <div class="relative">
                            <button
                                type="button"
                                @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100 text-indigo-700 font-semibold text-sm hover:ring-2 hover:ring-indigo-500 focus:outline-none transition-all"
                            >
                                {{ $initials ?: 'U' }}
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                x-show="userMenuOpen"
                                @click.outside="userMenuOpen = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-1 z-50 border border-gray-100"
                                style="display: none;"
                            >
                                <div class="px-4 py-2 border-b border-gray-100 sm:hidden">
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ $user->role?->label() ?? (is_string($user->role) ? ucfirst($user->role) : '') }}</p>
                                </div>

                                @if (Route::has('password.change'))
                                    <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        Change Password
                                    </a>
                                @endif

                                @if(Route::has('logout'))
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">
                                            Sign out
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </header>

            <!-- Main Content Slot -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <x-alert />
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>
</html>
