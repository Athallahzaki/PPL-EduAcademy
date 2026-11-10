<x-layouts.guest title="Change Password">
    <div class="mb-5 text-center">
        <p class="text-xs text-gray-500">
            For security, please enter your current (temporary) password and choose a new, secure password.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Current Password -->
        <div>
            <x-label for="current_password" value="Current password" required />
            <x-input
                id="current_password"
                type="password"
                name="current_password"
                required
                autocomplete="current-password"
                placeholder="Current or temporary password"
                :error="$errors->has('current_password')"
            />
            <x-input-error :messages="$errors->get('current_password')" />
        </div>

        <!-- New Password -->
        <div>
            <x-label for="password" value="New password (min 8 characters)" required />
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

        <!-- Confirm New Password -->
        <div>
            <x-label for="password_confirmation" value="Confirm new password" required />
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

        <div>
            <x-button class="w-full">
                Update Password
            </x-button>
        </div>
    </form>

    <div class="mt-6 border-t border-gray-100 pt-4 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 hover:underline">
                Sign out instead
            </button>
        </form>
    </div>
</x-layouts.guest>

