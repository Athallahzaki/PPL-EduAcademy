<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'admin_id',
        'title',
        'content',
        'image_url',
        'is_active',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the administrator who created the announcement.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Limit announcements to those currently visible on learner dashboards.
     *
     * @param  Builder<Announcement>  $query
     * @return Builder<Announcement>
     */
    public function scopeVisibleToDashboard(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('is_active', true)
            ->whereBetween('published_at', [$now->copy()->subMonths(6), $now]);
    }

    public function isArchived(): bool
    {
        return $this->published_at !== null
            && $this->published_at->lt(now()->subMonths(6));
    }
}
