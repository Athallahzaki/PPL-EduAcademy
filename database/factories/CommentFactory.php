<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\DiscussionThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'thread_id' => DiscussionThread::factory(),
            'user_id' => User::factory(),
            'parent_id' => null,
            'comment' => fake()->paragraph(),
        ];
    }

    /**
     * State indicating that this comment is a reply to another comment.
     */
    public function reply(?Comment $parent = null): static
    {
        return $this->state(function (array $attributes) use ($parent) {
            $parentComment = $parent ?? Comment::factory()->create();

            return [
                'thread_id' => $parentComment->thread_id,
                'parent_id' => $parentComment->id,
            ];
        });
    }
}
