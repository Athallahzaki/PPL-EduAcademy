<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory()->published(),
            'student_id' => User::factory()->student()->active(),
            'enrolled_at' => now(),
            'completed_at' => null,
        ];
    }
}

