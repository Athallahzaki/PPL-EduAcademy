<?php

namespace Database\Factories;

use App\Models\DiscussionThread;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiscussionThread>
 */
class DiscussionThreadFactory extends Factory
{
    protected $model = DiscussionThread::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student()->active(),
            'subject_id' => Subject::factory(),
            'title' => fake()->sentence(6),
            'content' => fake()->paragraphs(2, true),
        ];
    }
}
