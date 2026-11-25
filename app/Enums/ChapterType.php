<?php

namespace App\Enums;

enum ChapterType: string
{
    case Text = 'text';
    case Video = 'video';

    public function label(): string
    {
        return match ($this) {
            self::Text => 'Text',
            self::Video => 'Video',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Text => 'document-text',
            self::Video => 'video-camera',
        };
    }
}
