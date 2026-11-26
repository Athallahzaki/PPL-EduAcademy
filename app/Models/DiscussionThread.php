<?php

namespace App\Models;

use Database\Factories\DiscussionThreadFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscussionThread extends Model
{
    /** @use HasFactory<DiscussionThreadFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject_id',
        'title',
        'content',
    ];

    /**
     * Author of this discussion thread.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Subject/Category of this thread.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * All comments belonging to this discussion thread.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'thread_id');
    }

    /**
     * Root (top-level) comments belonging to this discussion thread.
     */
    public function rootComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'thread_id')
            ->whereNull('parent_id')
            ->latest('created_at');
    }

    /**
     * Scope: search by keyword in title, content, or author's name.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $term = '%' . trim($term) . '%';

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', $term)
                ->orWhere('content', 'like', $term)
                ->orWhereHas('user', function (Builder $userQ) use ($term) {
                    $userQ->where('name', 'like', $term);
                });
        });
    }

    /**
     * Scope: filter by subject id or slug.
     */
    public function scopeWithSubject(Builder $query, mixed $subject): Builder
    {
        if (empty($subject)) {
            return $query;
        }

        if (is_numeric($subject)) {
            return $query->where('subject_id', (int) $subject);
        }

        return $query->whereHas('subject', function (Builder $q) use ($subject) {
            $q->where('slug', $subject)->orWhere('name', $subject);
        });
    }
}
