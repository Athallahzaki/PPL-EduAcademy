<?php

namespace Tests\Unit;

use App\Support\MarkdownRenderer;
use Tests\TestCase;

class MarkdownRendererTest extends TestCase
{
    public function test_renders_markdown_headings_lists_and_code(): void
    {
        $markdown = "# Main Title\n\n- Item 1\n- Item 2\n\n```php\necho 'hello';\n```";
        $html = (string) MarkdownRenderer::render($markdown);

        $this->assertStringContainsString('<h1>Main Title</h1>', $html);
        $this->assertStringContainsString('<ul>', $html);
        $this->assertStringContainsString('<li>Item 1</li>', $html);
        $this->assertStringContainsString('<code', $html);
    }

    public function test_strips_raw_script_tags(): void
    {
        $markdown = "Hello <script>alert('XSS')</script> world";
        $html = (string) MarkdownRenderer::render($markdown);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('</script>', $html);
        $this->assertStringNotContainsString('<script', $html);
    }

    public function test_strips_raw_html_tags(): void
    {
        $markdown = "Hello <div onclick=\"alert(1)\">Dangerous</div> <iframe src=\"https://evil.com\"></iframe>";
        $html = (string) MarkdownRenderer::render($markdown);

        $this->assertStringNotContainsString('<div', $html);
        $this->assertStringNotContainsString('<iframe', $html);
    }

    public function test_blocks_unsafe_javascript_links(): void
    {
        $markdown = "[Click here](javascript:alert(1))";
        $html = (string) MarkdownRenderer::render($markdown);

        $this->assertStringNotContainsString('href="javascript:alert(1)"', $html);
    }

    public function test_handles_null_or_empty_markdown(): void
    {
        $this->assertSame('', (string) MarkdownRenderer::render(null));
        $this->assertSame('', (string) MarkdownRenderer::render(''));
        $this->assertSame('', (string) MarkdownRenderer::render('   '));
    }

    public function test_external_links_have_rel_and_target(): void
    {
        $markdown = "[External Link](https://external-example.com/docs)";
        $html = (string) MarkdownRenderer::render($markdown);

        $this->assertStringContainsString('href="https://external-example.com/docs"', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="', $html);
        $this->assertStringContainsString('noopener', $html);
    }
}
