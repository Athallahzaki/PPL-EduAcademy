<?php

namespace App\Support;

class VideoLink
{
    public function __construct(
        public readonly string $provider,
        public readonly string $originalUrl,
        public readonly ?string $embedUrl = null,
    ) {}

    public function isEmbeddable(): bool
    {
        return $this->embedUrl !== null;
    }

    public function isYouTube(): bool
    {
        return $this->provider === 'youtube';
    }

    public function isVimeo(): bool
    {
        return $this->provider === 'vimeo';
    }

    public function isOther(): bool
    {
        return $this->provider === 'other';
    }
}
