<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\DB;

trait HasPosition
{
    /**
     * Boot the position trait.
     */
    public static function bootHasPosition(): void
    {
        static::creating(function ($model) {
            if ($model->position === null) {
                $parentCol = $model->getPositionParentColumn();
                $maxPosition = static::where($parentCol, $model->{$parentCol})->max('position');
                $model->position = ($maxPosition ?? 0) + 1;
            }
        });

        static::deleted(function ($model) {
            $model->resequenceSiblings();
        });
    }

    /**
     * Get the foreign key column identifying the parent container.
     */
    abstract public function getPositionParentColumn(): string;

    /**
     * Resequence all siblings to contiguous 1..n positions.
     */
    public function resequenceSiblings(): void
    {
        DB::transaction(function () {
            $parentCol = $this->getPositionParentColumn();
            $parentId = $this->{$parentCol};

            $siblings = static::where($parentCol, $parentId)
                ->orderBy('position')
                ->lockForUpdate()
                ->get();

            foreach ($siblings as $index => $sibling) {
                $targetPos = $index + 1;
                if ($sibling->position !== $targetPos) {
                    static::where('id', $sibling->id)->update(['position' => $targetPos]);
                }
            }
        });
    }

    /**
     * Move this item up one position among its siblings.
     */
    public function moveUp(): bool
    {
        return DB::transaction(function () {
            $parentCol = $this->getPositionParentColumn();
            $parentId = $this->{$parentCol};

            $siblings = static::where($parentCol, $parentId)
                ->orderBy('position')
                ->lockForUpdate()
                ->get();

            // Resequence first if needed
            foreach ($siblings as $index => $sibling) {
                $sibling->position = $index + 1;
                static::where('id', $sibling->id)->update(['position' => $index + 1]);
            }

            $currentIndex = $siblings->search(fn ($item) => $item->id === $this->id);

            if ($currentIndex === false || $currentIndex === 0) {
                return false; // Already at top or not found
            }

            $previous = $siblings[$currentIndex - 1];

            $temp = $this->position;
            $this->position = $previous->position;
            $previous->position = $temp;

            static::where('id', $previous->id)->update(['position' => $previous->position]);
            static::where('id', $this->id)->update(['position' => $this->position]);

            return true;
        });
    }

    /**
     * Move this item down one position among its siblings.
     */
    public function moveDown(): bool
    {
        return DB::transaction(function () {
            $parentCol = $this->getPositionParentColumn();
            $parentId = $this->{$parentCol};

            $siblings = static::where($parentCol, $parentId)
                ->orderBy('position')
                ->lockForUpdate()
                ->get();

            // Resequence first if needed
            foreach ($siblings as $index => $sibling) {
                $sibling->position = $index + 1;
                static::where('id', $sibling->id)->update(['position' => $index + 1]);
            }

            $currentIndex = $siblings->search(fn ($item) => $item->id === $this->id);

            if ($currentIndex === false || $currentIndex >= $siblings->count() - 1) {
                return false; // Already at bottom or not found
            }

            $next = $siblings[$currentIndex + 1];

            $temp = $this->position;
            $this->position = $next->position;
            $next->position = $temp;

            static::where('id', $next->id)->update(['position' => $next->position]);
            static::where('id', $this->id)->update(['position' => $this->position]);

            return true;
        });
    }

    /**
     * Move item in the given direction ('up' or 'down').
     */
    public function move(string $direction): bool
    {
        return match ($direction) {
            'up' => $this->moveUp(),
            'down' => $this->moveDown(),
            default => false,
        };
    }
}
