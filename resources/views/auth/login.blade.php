<x-layouts.guest title="Sign in to your account">
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-label for="email" value="Email address" required />
            <x-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="name@example.com"
                :error="$errors->has('email')"
            />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-label for="password" value="Password" required />
                <button
                    type="button"
                    x-data
                    @click="$dispatch('open-modal', 'forgot-password-modal')"
                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 hover:underline"
                >
                    Forgot password?
                </button>
            </div>
            <x-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                :error="$errors->has('password')"
            />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input
                id="remember"
                type="checkbox"
                name="remember"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            />
            <label for="remember" class="ml-2 block text-xs text-gray-700">
                Remember me on this device
            </label>
        </div>

        <!-- Submit Button -->
        <div>
            <x-button class="w-full">
                Sign in
            </x-button>
        </div>
    </form>

    <div class="mt-6 border-t border-gray-100 pt-6 text-center text-xs text-gray-500">
        Don't have an account yet?
        <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline">
            Create an account
        </a>
    </div>

    <!-- Forgot Password Modal (§5.2 / §38) -->
    <x-modal name="forgot-password-modal" title="Reset Your Password">
        <div class="space-y-4">
            <div class="flex items-start gap-3">
                <div class="rounded-lg bg-indigo-50 p-2 text-indigo-600 shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900">Administrator Assistance Required</h4>
                    <p class="mt-1 text-sm text-gray-600 leading-relaxed">
                        Forgot your password? Please contact an administrator to reset it. For security purposes, self-service email password recovery is not available.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'forgot-password-modal')">
                    Close
                </x-button>
            </div>
        </div>
    </x-modal>
</x-layouts.guest>

