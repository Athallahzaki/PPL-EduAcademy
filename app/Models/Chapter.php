<?php

namespace App\Models;

use App\Enums\ChapterType;
use App\Models\Concerns\HasPosition;
use App\Support\VideoEmbed;
use App\Support\VideoLink;
use Database\Factories\ChapterFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chapter extends Model
{
    /** @use HasFactory<ChapterFactory> */
    use HasFactory, HasPosition;

    /**
     * Mass assignable attributes.
     * Note: module_id, position, and is_published MUST NOT be mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'type',
        'body',
        'video_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ChapterType::class,
            'is_published' => 'boolean',
            'position' => 'integer',
        ];
    }

    /**
     * Module that owns the chapter.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Scope for published chapters.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Parsed video link value object or null.
     */
    protected function video(): Attribute
    {
        return Attribute::make(
            get: fn (): ?VideoLink => VideoEmbed::parse($this->video_url),
        );
    }

    /**
     * Determine if chapter is visible to students (both chapter and parent module must be published).
     */
    public function isVisibleToStudents(): bool
    {
        return $this->is_published && ($this->module?->is_published ?? false);
    }

    /**
     * Parent foreign key column for HasPosition trait.
     */
    public function getPositionParentColumn(): string
    {
        return 'module_id';
    }
}
