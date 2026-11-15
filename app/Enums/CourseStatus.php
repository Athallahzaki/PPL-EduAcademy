<?php

namespace App\Enums;

enum CourseStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'green',
            self::Archived => 'amber',
        };
    }

    public function canTransitionTo(self $to, int $enrollmentsCount = 0): bool
    {
        return match ($this) {
            self::Draft => $to === self::Published,
            self::Published => match ($to) {
                self::Archived => true,
                self::Draft => $enrollmentsCount === 0,
                default => false,
            },
            self::Archived => $to === self::Published,
        };
    }
}

