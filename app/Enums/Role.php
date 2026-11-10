<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Instructor = 'instructor';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Instructor => 'Instructor',
            self::Student => 'Student',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Instructor => 'instructor.dashboard',
            self::Student => 'student.dashboard',
        };
    }
}

