<?php

namespace Database\Factories;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'instructor_id' => User::factory()->instructor()->active(),
            'subject_id' => Subject::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(2, true),
            'level' => CourseLevel::Beginner,
            'banner_path' => null,
            'status' => CourseStatus::Draft,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => CourseStatus::Draft,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => CourseStatus::Published,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => CourseStatus::Archived,
        ]);
    }

    public function level(CourseLevel $level): static
    {
        return $this->state(fn () => [
            'level' => $level,
        ]);
    }
}
