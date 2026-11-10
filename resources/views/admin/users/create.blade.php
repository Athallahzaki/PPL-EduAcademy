<x-layouts.app title="Create User Account">
    <div class="max-w-2xl mx-auto space-y-6">
        <x-page-header title="Create User Account" description="Add a new administrator, instructor, or student account to the academy." />

        <x-card>
            <div class="mb-6 rounded-xl border border-indigo-100 bg-indigo-50/60 p-4 text-xs text-indigo-900 leading-relaxed flex items-start gap-3">
                <div class="p-1 rounded-md bg-indigo-100 text-indigo-700 shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </div>
                <div>
                    <strong>Temporary Password Delivery:</strong> A secure temporary password will be automatically generated upon creation and displayed to you once. The user will be forced to change this temporary password upon their first login.
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <x-label for="name" value="Full Name" required />
                    <x-input
                        id="name"
                        type="text"
                        name="name"
                        :value="old('name')"
                        required
                        autofocus
                        placeholder="John Smith"
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
                        :value="old('email')"
                        required
                        placeholder="john@example.com"
                        :error="$errors->has('email')"
                    />
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <!-- Role -->
                <div>
                    <x-label for="role" value="Account Role" required />
                    <x-select id="role" name="role" required :error="$errors->has('role')">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select an account role</option>
                        <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instructor</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    </x-select>
                    <x-input-error :messages="$errors->get('role')" />
                </div>

                <!-- Submit / Cancel -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </a>
                    <x-button type="submit">
                        Create Account
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>

