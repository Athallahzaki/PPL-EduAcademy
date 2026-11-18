<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\ScholarshipStatus;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Scholarship>
 */
class ScholarshipFactory extends Factory
{
    protected $model = Scholarship::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => User::factory()->state(['role' => Role::Admin]),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'requirements' => "1. Warga Negara Indonesia.\n2. Mahasiswa/Siswa aktif.\n3. Melampirkan berkas portofolio dan CV.\n4. Memiliki motivasi belajar yang tinggi.",
            'quota' => fake()->numberBetween(10, 100),
            'start_date' => now()->subDays(5)->toDateString(),
            'deadline' => now()->addDays(30)->toDateString(),
            'status' => ScholarshipStatus::Published,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => ScholarshipStatus::Draft,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => ScholarshipStatus::Published,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => ScholarshipStatus::Closed,
            'deadline' => now()->subDays(5)->toDateString(),
        ]);
    }
}
