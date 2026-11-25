<?php

namespace Database\Factories;

use App\Enums\ChapterType;
use App\Models\Chapter;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chapter>
 */
class ChapterFactory extends Factory
{
    protected $model = Chapter::class;

    /**
     * Define the model's default state (Text by default).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'module_id' => Module::factory(),
            'title' => fake()->sentence(4),
            'type' => ChapterType::Text,
            'body' => "# Introduction\n\n" . fake()->paragraphs(3, true),
            'video_url' => null,
            'position' => null, // Computed automatically by HasPosition trait
            'is_published' => false,
        ];
    }

    /**
     * State for a text chapter.
     */
    public function text(?string $body = null): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ChapterType::Text,
            'body' => $body ?? ("# Text Chapter\n\n" . fake()->paragraphs(3, true)),
            'video_url' => null,
        ]);
    }

    /**
     * State for a video chapter.
     */
    public function video(?string $url = null, ?string $notes = null): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ChapterType::Video,
            'video_url' => $url ?? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'body' => $notes ?? fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the chapter is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }
}
