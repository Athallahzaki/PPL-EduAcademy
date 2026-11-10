<x-layouts.guest title="Create your EduAcademy account">
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Full Name -->
        <div>
            <x-label for="name" value="Full name" required />
            <x-input
                id="name"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                placeholder="Jane Doe"
                :error="$errors->has('name')"
            />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <!-- Email Address -->
        <div>
            <x-label for="email" value="Email address" required />
            <x-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="email"
                placeholder="jane@example.com"
                :error="$errors->has('email')"
            />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div>
            <x-label for="password" value="Password (min 8 characters)" required />
            <x-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="••••••••"
                :error="$errors->has('password')"
            />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Password Confirmation -->
        <div>
            <x-label for="password_confirmation" value="Confirm password" required />
            <x-input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="••••••••"
                :error="$errors->has('password_confirmation')"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <!-- Register As (Role Selection) -->
        <div>
            <x-label value="Register as" required />
            <div class="grid grid-cols-2 gap-3 mt-1.5">
                <label class="relative flex flex-col p-3.5 border rounded-xl cursor-pointer hover:border-indigo-300 transition-colors {{ old('register_as', 'student') === 'student' ? 'border-indigo-600 bg-indigo-50/30 ring-1 ring-indigo-600' : 'border-gray-200' }}">
                    <div class="flex items-center gap-2">
                        <input
                            type="radio"
                            name="register_as"
                            value="student"
                            class="text-indigo-600 focus:ring-indigo-500"
                            {{ old('register_as', 'student') === 'student' ? 'checked' : '' }}
                        />
                        <span class="text-sm font-semibold text-gray-900">Student</span>
                    </div>
                    <span class="mt-1 text-xs text-gray-500 pl-5">Immediate access after onboarding.</span>
                </label>

                <label class="relative flex flex-col p-3.5 border rounded-xl cursor-pointer hover:border-indigo-300 transition-colors {{ old('register_as') === 'instructor' ? 'border-indigo-600 bg-indigo-50/30 ring-1 ring-indigo-600' : 'border-gray-200' }}">
                    <div class="flex items-center gap-2">
                        <input
                            type="radio"
                            name="register_as"
                            value="instructor"
                            class="text-indigo-600 focus:ring-indigo-500"
                            {{ old('register_as') === 'instructor' ? 'checked' : '' }}
                        />
                        <span class="text-sm font-semibold text-gray-900">Instructor</span>
                    </div>
                    <span class="mt-1 text-xs text-gray-500 pl-5">Requires administrator approval.</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('register_as')" />
        </div>

        <!-- Submit Button -->
        <div>
            <x-button class="w-full">
                Create account
            </x-button>
        </div>
    </form>

    <div class="mt-6 border-t border-gray-100 pt-6 text-center text-xs text-gray-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline">
            Sign in
        </a>
    </div>
</x-layouts.guest>

