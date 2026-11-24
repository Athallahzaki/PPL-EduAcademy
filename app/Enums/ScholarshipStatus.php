<?php

namespace App\Enums;

enum ScholarshipStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Closed => 'Closed',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'green',
            self::Closed => 'rose',
        };
    }
}
