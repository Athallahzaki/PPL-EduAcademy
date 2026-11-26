<div x-data="{ open: false }" class="border border-indigo-100 bg-indigo-50/50 rounded-lg text-xs p-3">
    <button type="button" @click="open = !open" class="flex items-center justify-between w-full font-semibold text-indigo-900 focus:outline-none">
        <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Markdown Formatting Guide
        </span>
        <span class="text-indigo-600 font-normal hover:underline" x-text="open ? 'Hide cheat sheet' : 'Show cheat sheet'"></span>
    </button>

    <div x-show="open" x-cloak class="mt-3 pt-3 border-t border-indigo-100 grid grid-cols-1 sm:grid-cols-2 gap-3 text-gray-700">
        <div>
            <p class="font-bold text-gray-900">Headings & Formatting</p>
            <ul class="space-y-1 mt-1 font-mono text-[11px] text-gray-600">
                <li># Heading 1</li>
                <li>## Heading 2</li>
                <li>### Heading 3</li>
                <li>**Bold** · *Italic*</li>
            </ul>
        </div>
        <div>
            <p class="font-bold text-gray-900">Lists & Quotes</p>
            <ul class="space-y-1 mt-1 font-mono text-[11px] text-gray-600">
                <li>- Bullet point</li>
                <li>1. Numbered item</li>
                <li>> Blockquote quote</li>
            </ul>
        </div>
        <div>
            <p class="font-bold text-gray-900">Links & Images</p>
            <ul class="space-y-1 mt-1 font-mono text-[11px] text-gray-600">
                <li>[Link text](https://example.com)</li>
                <li>![Alt text](https://image-url.com)</li>
            </ul>
        </div>
        <div>
            <p class="font-bold text-gray-900">Code Blocks</p>
            <ul class="space-y-1 mt-1 font-mono text-[11px] text-gray-600">
                <li>`inline code`</li>
                <li>```php<br>// code snippet<br>```</li>
            </ul>
        </div>
    </div>
</div>
