<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;

class MarkdownRenderer
{
    /**
     * Render raw markdown text to a safe HtmlString.
     */
    public static function render(?string $markdown): HtmlString
    {
        if ($markdown === null || trim($markdown) === '') {
            return new HtmlString('');
        }

        $options = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 20,
        ];

        $extensions = [];

        // Check if ExternalLinkExtension is available
        if (class_exists(ExternalLinkExtension::class)) {
            $extensions[] = new ExternalLinkExtension();
            $options['external_link'] = [
                'internal_hosts' => [
                    parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?? 'localhost',
                ],
                'open_in_new_window' => true,
                'nofollow' => 'all',
                'noopener' => 'all',
                'noreferrer' => 'all',
            ];
        }

        $html = Str::markdown($markdown, $options, $extensions);

        return new HtmlString($html);
    }
}
