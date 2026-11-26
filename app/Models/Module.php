<?php

namespace App\Models;

use App\Models\Concerns\HasPosition;
use Database\Factories\ModuleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    /** @use HasFactory<ModuleFactory> */
    use HasFactory, HasPosition;

    /**
     * Mass assignable attributes.
     * Note: course_id, position, and is_published MUST NOT be mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'position' => 'integer',
        ];
    }

    /**
     * Course that owns the module.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Chapters belonging to this module, ordered by position.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('position');
    }

    /**
     * Scope for published modules.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Deletion safety check hook.
     * PBI-13 will extend this to check for student submissions.
     */
    public function canBeDeleted(): bool
    {
        return true;
    }

    /**
     * Reason why this module cannot be deleted, or null if it can.
     * PBI-13 will extend this hook.
     */
    public function deletionBlockedReason(): ?string
    {
        return null;
    }

    /**
     * Parent foreign key column for HasPosition trait.
     */
    public function getPositionParentColumn(): string
    {
        return 'course_id';
    }
}
