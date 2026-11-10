<x-layouts.app title="User: {{ $user->name }}">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <x-page-header title="{{ $user->name }}" description="Detailed account overview and management actions.">
            <x-slot:actions>
                <a
                    href="{{ route('admin.users.index') }}"
                    class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    &larr; Back to Accounts
                </a>
                <a
                    href="{{ route('admin.users.edit', $user) }}"
                    class="rounded-lg bg-indigo-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors"
                >
                    Edit Details
                </a>
            </x-slot:actions>
        </x-page-header>

        <!-- User Information Card -->
        <x-card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Account Overview</h3>
                    <div class="flex items-center gap-2">
                        <x-badge :variant="$user->role?->value ?? 'gray'">
                            {{ $user->role?->label() ?? ucfirst($user->role ?? '') }}
                        </x-badge>
                        <x-badge :variant="$user->status?->badgeColor() ?? 'gray'">
                            {{ $user->status?->label() ?? ucfirst($user->status ?? '') }}
                        </x-badge>
                    </div>
                </div>
            </x-slot:header>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Account ID</dt>
                    <dd class="mt-1 font-mono text-gray-900">#{{ $user->id }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</dt>
                    <dd class="mt-1 text-gray-900">
                        <x-user-name :user="$user" />
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email Address</dt>
                    <dd class="mt-1 font-mono text-gray-900">{{ $user->email }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Onboarding State</dt>
                    <dd class="mt-1 text-gray-900">
                        @if ($user->onboarded_at)
                            <span class="inline-flex items-center gap-1.5 text-emerald-700 font-medium text-xs">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Completed on {{ $user->onboarded_at->format('M d, Y H:i') }} WIB
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-amber-700 font-medium text-xs">
                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                Pending (Not finished yet)
                            </span>
                        @endif
                    </dd>
                </div>

                @if ($user->education_level)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Education Level</dt>
                        <dd class="mt-1 text-gray-900 capitalize">{{ str_replace('_', ' ', $user->education_level) }}</dd>
                    </div>
                @endif

                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Must Change Password</dt>
                    <dd class="mt-1 text-gray-900">
                        @if ($user->must_change_password)
                            <span class="text-rose-600 font-semibold text-xs">Yes (Forced on next login)</span>
                        @else
                            <span class="text-gray-500 text-xs">No</span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registration Date</dt>
                    <dd class="mt-1 text-gray-900">{{ $user->created_at->format('M d, Y H:i') }} WIB</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Last Profile Update</dt>
                    <dd class="mt-1 text-gray-900">{{ $user->updated_at->format('M d, Y H:i') }} WIB</dd>
                </div>
            </dl>
        </x-card>

        <!-- Instructor Profile Details (if exists) -->
        @if ($user->instructorProfile)
            <x-card>
                <x-slot:header>
                    <h3 class="text-base font-semibold text-gray-900">Instructor Profile Details</h3>
                </x-slot:header>

                <div class="space-y-4 text-sm">
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Expertise</h4>
                        <p class="mt-1 font-medium text-gray-900">{{ $user->instructorProfile->expertise }}</p>
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Professional Bio</h4>
                        <p class="mt-1 text-gray-700 leading-relaxed whitespace-pre-line">{{ $user->instructorProfile->bio }}</p>
                    </div>

                    @if ($user->instructorProfile->portfolio_url)
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Portfolio Link</h4>
                            <p class="mt-1">
                                <a href="{{ $user->instructorProfile->portfolio_url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline">
                                    {{ $user->instructorProfile->portfolio_url }} &nearr;
                                </a>
                            </p>
                        </div>
                    @endif

                    @if ($user->instructorProfile->cv_path)
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Curriculum Vitae</h4>
                            <p class="mt-1 text-xs text-gray-500">
                                Archived on private storage: <code class="bg-gray-100 px-2 py-0.5 rounded text-gray-700 font-mono">{{ $user->instructorProfile->cv_path }}</code>
                                <span class="text-gray-400 block mt-0.5">(Preview / download deferred to PBI-04)</span>
                            </p>
                        </div>
                    @endif
                </div>
            </x-card>
        @endif

        <!-- Action Controls Card -->
        <x-card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Administrative Actions</h3>
            </x-slot:header>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Status Actions -->
                @if ($user->status === \App\Enums\AccountStatus::Pending)
                    <form method="POST" action="{{ route('admin.users.status', $user) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="active">
                        <x-button type="submit" variant="success">
                            Approve Instructor
                        </x-button>
                    </form>

                    <form method="POST" action="{{ route('admin.users.status', $user) }}" onsubmit="return confirm('Reject this instructor registration?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <x-button type="submit" variant="secondary">
                            Reject Registration
                        </x-button>
                    </form>
                @elseif ($user->status === \App\Enums\AccountStatus::Active && auth()->id() !== $user->id)
                    <form method="POST" action="{{ route('admin.users.status', $user) }}" onsubmit="return confirm('Suspend this user account? All active sessions will be terminated immediately.');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="suspended">
                        <x-button type="submit" variant="danger">
                            Suspend Account
                        </x-button>
                    </form>
                @elseif (in_array($user->status, [\App\Enums\AccountStatus::Suspended, \App\Enums\AccountStatus::Rejected]))
                    <form method="POST" action="{{ route('admin.users.status', $user) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="active">
                        <x-button type="submit" variant="success">
                            Reactivate Account
                        </x-button>
                    </form>
                @endif

                <!-- Reset Password (admin cannot reset their own password via admin panel) -->
                @if (auth()->id() !== $user->id)
                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" onsubmit="return confirm('Reset this user\'s password? A new temporary password will be generated and existing sessions invalidated.');">
                        @csrf
                        <x-button type="submit" variant="warning">
                            Reset Password (Generate Temporary)
                        </x-button>
                    </form>
                @endif

                <!-- Delete Account (soft delete) -->
                @if (auth()->id() !== $user->id && !($user->isAdmin() && $user->isActive() && \App\Models\User::activeAdminCount() <= 1))
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Soft-delete this user account? The user will be signed out and unable to log in.');">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="secondary" class="text-rose-600 hover:bg-rose-50 border-rose-200">
                            Delete Account
                        </x-button>
                    </form>
                @endif
            </div>
        </x-card>
    </div>
</x-layouts.app>

