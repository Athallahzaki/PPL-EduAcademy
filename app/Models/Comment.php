<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'thread_id',
        'user_id',
        'parent_id',
        'comment',
    ];

    /**
     * The discussion thread this comment belongs to.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(DiscussionThread::class, 'thread_id');
    }

    /**
     * Author of this comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parent comment if this is a nested reply.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Direct replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->oldest('created_at');
    }

    /**
     * Check if this comment is a top-level (root) comment.
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if this comment is a reply to another comment.
     */
    public function isReply(): bool
    {
        return ! is_null($this->parent_id);
    }

    /**
     * Calculate nesting depth of this comment (0 for root, 1 for 1st reply, etc.).
     */
    public function depth(): int
    {
        $depth = 0;
        $current = $this;

        while ($current->parent !== null) {
            $depth++;
            $current = $current->parent;
        }

        return $depth;
    }
}
