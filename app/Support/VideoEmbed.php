<?php

namespace App\Support;

class VideoEmbed
{
    /**
     * Allowed YouTube hostnames (exact match, case-insensitive).
     */
    protected const YOUTUBE_HOSTS = [
        'youtube.com',
        'www.youtube.com',
        'm.youtube.com',
        'youtube-nocookie.com',
        'www.youtube-nocookie.com',
        'youtu.be',
    ];

    /**
     * Allowed Vimeo hostnames (exact match, case-insensitive).
     */
    protected const VIMEO_HOSTS = [
        'vimeo.com',
        'www.vimeo.com',
        'player.vimeo.com',
    ];

    /**
     * Parse a video URL and return a VideoLink object or null if invalid.
     */
    public static function parse(?string $url): ?VideoLink
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        // Validate basic URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($url);
        if ($parts === false || !isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        $scheme = strtolower($parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower($parts['host']);

        // Check YouTube
        if (in_array($host, self::YOUTUBE_HOSTS, true)) {
            $videoId = self::extractYouTubeId($host, $parts);
            if ($videoId !== null && preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)) {
                return new VideoLink(
                    provider: 'youtube',
                    originalUrl: $url,
                    embedUrl: "https://www.youtube-nocookie.com/embed/{$videoId}",
                );
            }

            return new VideoLink(
                provider: 'other',
                originalUrl: $url,
                embedUrl: null,
            );
        }

        // Check Vimeo
        if (in_array($host, self::VIMEO_HOSTS, true)) {
            $videoId = self::extractVimeoId($parts);
            if ($videoId !== null && preg_match('/^[0-9]+$/', $videoId)) {
                return new VideoLink(
                    provider: 'vimeo',
                    originalUrl: $url,
                    embedUrl: "https://player.vimeo.com/video/{$videoId}",
                );
            }

            return new VideoLink(
                provider: 'other',
                originalUrl: $url,
                embedUrl: null,
            );
        }

        // Any other valid http/https URL
        return new VideoLink(
            provider: 'other',
            originalUrl: $url,
            embedUrl: null,
        );
    }

    /**
     * Extract 11-char YouTube ID from url parts.
     */
    protected static function extractYouTubeId(string $host, array $parts): ?string
    {
        $path = $parts['path'] ?? '';

        if ($host === 'youtu.be') {
            $segments = explode('/', trim($path, '/'));
            return $segments[0] ?? null;
        }

        // watch?v=ID
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (!empty($query['v']) && is_string($query['v'])) {
                return $query['v'];
            }
        }

        // embed/ID or shorts/ID
        $trimmedPath = trim($path, '/');
        if (preg_match('/^(?:embed|shorts)\/([A-Za-z0-9_-]{11})/i', $trimmedPath, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extract numeric Vimeo ID from url parts.
     */
    protected static function extractVimeoId(array $parts): ?string
    {
        $path = trim($parts['path'] ?? '', '/');

        // /video/{ID} or /{ID}
        if (preg_match('/^(?:video\/)?([0-9]+)/i', $path, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
