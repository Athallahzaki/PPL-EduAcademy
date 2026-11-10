<?php

namespace Database\Factories;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => Role::Student,
            'status' => AccountStatus::Active,
            'must_change_password' => false,
            'onboarded_at' => now(),
            'education_level' => 'university',
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Admin,
            'status' => AccountStatus::Active,
            'onboarded_at' => now(),
            'education_level' => null,
        ]);
    }

    public function instructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Instructor,
            'education_level' => null,
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Student,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccountStatus::Pending,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccountStatus::Active,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccountStatus::Suspended,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccountStatus::Rejected,
        ]);
    }

    public function unonboarded(): static
    {
        return $this->state(fn (array $attributes) => [
            'onboarded_at' => null,
        ]);
    }

    public function mustChangePassword(): static
    {
        return $this->state(fn (array $attributes) => [
            'must_change_password' => true,
        ]);
    }
}
