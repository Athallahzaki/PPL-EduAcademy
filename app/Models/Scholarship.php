<?php

namespace App\Models;

use App\Enums\ScholarshipStatus;
use Database\Factories\ScholarshipFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scholarship extends Model
{
    /** @use HasFactory<ScholarshipFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'admin_id',
        'title',
        'description',
        'requirements',
        'quota',
        'start_date',
        'deadline',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quota' => 'integer',
            'start_date' => 'date',
            'deadline' => 'date',
            'status' => ScholarshipStatus::class,
        ];
    }

    /**
     * The administrator who published the scholarship.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope for published scholarships.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ScholarshipStatus::Published->value);
    }

    /**
     * Scope for active scholarships (published and deadline today or future).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ScholarshipStatus::Published->value)
            ->whereDate('deadline', '>=', today());
    }

    /**
     * Filter by status.
     */
    public function scopeWithStatus(Builder $query, ScholarshipStatus|string|null $status): Builder
    {
        if (empty($status)) {
            return $query;
        }

        $value = $status instanceof ScholarshipStatus ? $status->value : $status;

        return $query->where('status', $value);
    }

    /**
     * Search scholarships by title, requirements, or description.
     */
    public function scopeSearch(Builder $query, ?string $q): Builder
    {
        if (blank($q)) {
            return $query;
        }

        $term = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($q));

        return $query->where(function (Builder $sub) use ($term) {
            $sub->where('title', 'like', "%{$term}%")
                ->orWhere('requirements', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Check if the scholarship is currently open for applications.
     */
    public function isOpen(): bool
    {
        if ($this->status !== ScholarshipStatus::Published) {
            return false;
        }

        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }

        return $this->deadline === null || $this->deadline->isFuture() || $this->deadline->isToday();
    }

    /**
     * Check if deadline has passed.
     */
    public function isExpired(): bool
    {
        return $this->deadline !== null && $this->deadline->isPast() && ! $this->deadline->isToday();
    }

    /**
     * Calculate days remaining until deadline.
     */
    public function daysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return (int) today()->diffInDays($this->deadline, false);
    }
}
