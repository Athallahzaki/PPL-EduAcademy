<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = env('SEED_ADMIN_NAME', 'System Admin');
        $email = env('SEED_ADMIN_EMAIL', 'admin@eduacademy.test');
        $password = env('SEED_ADMIN_PASSWORD');

        if (! app()->environment('local') && empty($password)) {
            throw new RuntimeException('SEED_ADMIN_PASSWORD is required in non-local environments.');
        }

        // Fall back to default in local environment
        $password = $password ?: 'password';

        User::updateOrCreate(
            ['email' => strtolower(trim($email))],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => Role::Admin,
                'status' => AccountStatus::Active,
                'must_change_password' => false,
                'onboarded_at' => now(),
            ]
        );
    }
}

