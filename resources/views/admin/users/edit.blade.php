<x-layouts.app title="Edit User Account">
    <div class="max-w-2xl mx-auto space-y-6">
        <x-page-header title="Edit User: {{ $user->name }}" description="Update personal details and manage access role for this account.">
            <x-slot:actions>
                <a
                    href="{{ route('admin.users.show', $user) }}"
                    class="rounded-lg border border-gray-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    &larr; Back to Details
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-card>
            <!-- Role change warning (§6.3) -->
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900 leading-relaxed flex items-start gap-3">
                <div class="p-1 rounded-md bg-amber-100 text-amber-700 shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <strong>Access Warning:</strong> Changing a user's role affects what the user can access and immediately alters their dashboard, sidebar navigation, and platform permissions.
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <x-label for="name" value="Full Name" required />
                    <x-input
                        id="name"
                        type="text"
                        name="name"
                        :value="old('name', $user->name)"
                        required
                        autofocus
                        :error="$errors->has('name')"
                    />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <!-- Email -->
                <div>
                    <x-label for="email" value="Email Address" required />
                    <x-input
                        id="email"
                        type="email"
                        name="email"
                        :value="old('email', $user->email)"
                        required
                        :error="$errors->has('email')"
                    />
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <!-- Role -->
                <div>
                    <x-label for="role" value="Account Role" required />
                    @if (auth()->id() === $user->id)
                        <div class="rounded-lg bg-gray-50 border border-gray-200 px-3 py-2 text-sm text-gray-700">
                            {{ $user->role->label() }}
                            <span class="text-xs text-gray-500 ml-2">(You cannot change your own role)</span>
                            <input type="hidden" name="role" value="{{ $user->role->value }}">
                        </div>
                    @else
                        <x-select id="role" name="role" required :error="$errors->has('role')">
                            <option value="student" {{ old('role', $user->role->value) === 'student' ? 'selected' : '' }}>Student</option>
                            <option value="instructor" {{ old('role', $user->role->value) === 'instructor' ? 'selected' : '' }}>Instructor</option>
                            <option value="admin" {{ old('role', $user->role->value) === 'admin' ? 'selected' : '' }}>Administrator</option>
                        </x-select>
                        <x-input-error :messages="$errors->get('role')" />
                    @endif
                </div>

                <!-- Submit / Cancel -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a
                        href="{{ route('admin.users.show', $user) }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </a>
                    <x-button type="submit">
                        Save Changes
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>

