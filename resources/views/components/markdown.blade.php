@props(['content' => ''])

<div {{ $attributes->merge(['class' => 'markdown-body text-gray-700 leading-relaxed']) }}>
    {!! \App\Support\MarkdownRenderer::render($content) !!}
</div>
