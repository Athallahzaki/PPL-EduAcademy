@props([
    'isPublished' => false,
    'action' => '',
    'label' => 'Item',
])

<form action="{{ $action }}" method="POST" class="inline">
    @csrf
    @method('PATCH')
    <input type="hidden" name="is_published" value="{{ $isPublished ? '0' : '1' }}">

    @if($isPublished)
        <button type="submit"
            title="Click to unpublish this {{ strtolower($label) }}"
            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-md transition-colors">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            Published · Unpublish
        </button>
    @else
        <button type="submit"
            title="Click to publish this {{ strtolower($label) }}"
            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-gray-200 rounded-md transition-colors">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
            Draft · Publish
        </button>
    @endif
</form>
