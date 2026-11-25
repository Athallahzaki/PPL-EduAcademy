<?php

namespace Tests\Unit;

use App\Support\VideoEmbed;
use PHPUnit\Framework\TestCase;

class VideoEmbedTest extends TestCase
{
    public function test_parses_standard_youtube_url(): void
    {
        $link = VideoEmbed::parse('https://www.youtube.com/watch?v=dQw4w9WgXcQ');

        $this->assertNotNull($link);
        $this->assertTrue($link->isYouTube());
        $this->assertTrue($link->isEmbeddable());
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $link->embedUrl);
    }

    public function test_parses_shortened_youtu_be_url(): void
    {
        $link = VideoEmbed::parse('https://youtu.be/dQw4w9WgXcQ');

        $this->assertNotNull($link);
        $this->assertTrue($link->isYouTube());
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $link->embedUrl);
    }

    public function test_parses_mobile_and_shorts_and_embed_youtube_urls(): void
    {
        $mobile = VideoEmbed::parse('https://m.youtube.com/watch?v=dQw4w9WgXcQ');
        $this->assertNotNull($mobile);
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $mobile->embedUrl);

        $shorts = VideoEmbed::parse('https://www.youtube.com/shorts/dQw4w9WgXcQ');
        $this->assertNotNull($shorts);
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $shorts->embedUrl);

        $embed = VideoEmbed::parse('https://www.youtube.com/embed/dQw4w9WgXcQ');
        $this->assertNotNull($embed);
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $embed->embedUrl);
    }

    public function test_parses_vimeo_urls(): void
    {
        $vimeo1 = VideoEmbed::parse('https://vimeo.com/76979871');
        $this->assertNotNull($vimeo1);
        $this->assertTrue($vimeo1->isVimeo());
        $this->assertSame('https://player.vimeo.com/video/76979871', $vimeo1->embedUrl);

        $vimeo2 = VideoEmbed::parse('https://player.vimeo.com/video/76979871');
        $this->assertNotNull($vimeo2);
        $this->assertTrue($vimeo2->isVimeo());
        $this->assertSame('https://player.vimeo.com/video/76979871', $vimeo2->embedUrl);
    }

    public function test_rejects_lookalike_hosts(): void
    {
        $lookalike = VideoEmbed::parse('https://youtube.com.evil.test/watch?v=dQw4w9WgXcQ');
        $this->assertNotNull($lookalike);
        $this->assertTrue($lookalike->isOther());
        $this->assertNull($lookalike->embedUrl);

        $vimeoLookalike = VideoEmbed::parse('https://vimeo.com.attacker.com/76979871');
        $this->assertNotNull($vimeoLookalike);
        $this->assertTrue($vimeoLookalike->isOther());
        $this->assertNull($vimeoLookalike->embedUrl);
    }

    public function test_youtube_with_invalid_id_length_falls_back_to_other(): void
    {
        $invalid = VideoEmbed::parse('https://www.youtube.com/watch?v=tooShort');
        $this->assertNotNull($invalid);
        $this->assertTrue($invalid->isOther());
        $this->assertNull($invalid->embedUrl);
    }

    public function test_vimeo_with_non_numeric_id_falls_back_to_other(): void
    {
        $invalid = VideoEmbed::parse('https://vimeo.com/not-numbers');
        $this->assertNotNull($invalid);
        $this->assertTrue($invalid->isOther());
        $this->assertNull($invalid->embedUrl);
    }

    public function test_rejects_dangerous_or_invalid_schemes(): void
    {
        $this->assertNull(VideoEmbed::parse('javascript:alert(1)'));
        $this->assertNull(VideoEmbed::parse('data:text/html,<script>alert(1)</script>'));
        $this->assertNull(VideoEmbed::parse('ftp://example.com/video.mp4'));
        $this->assertNull(VideoEmbed::parse('not-a-url'));
        $this->assertNull(VideoEmbed::parse(null));
        $this->assertNull(VideoEmbed::parse('   '));
    }

    public function test_arbitrary_urls_parsed_as_other_without_embed_url(): void
    {
        $other = VideoEmbed::parse('https://example.com/tutorial-video');
        $this->assertNotNull($other);
        $this->assertTrue($other->isOther());
        $this->assertNull($other->embedUrl);
        $this->assertSame('https://example.com/tutorial-video', $other->originalUrl);
    }

    public function test_embed_url_contains_only_extracted_id(): void
    {
        $injectionAttempt = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ"onerror="alert(1)';
        $link = VideoEmbed::parse($injectionAttempt);
        $this->assertNotNull($link);
        // ID regex does not match due to extra quotes/chars, so it must not produce an embed URL
        $this->assertNull($link->embedUrl);
    }
}
