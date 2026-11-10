<?php

namespace App\Enums;

enum AccountStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Active => 'Active',
            self::Suspended => 'Suspended',
            self::Rejected => 'Rejected',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Active => 'green',
            self::Suspended => 'red',
            self::Rejected => 'gray',
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::Pending => in_array($to, [self::Active, self::Rejected], true),
            self::Active => in_array($to, [self::Suspended], true),
            self::Suspended => in_array($to, [self::Active], true),
            self::Rejected => in_array($to, [self::Active], true),
        };
    }
}

