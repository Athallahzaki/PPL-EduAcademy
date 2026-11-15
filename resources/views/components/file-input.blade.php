@props([
    'name' => 'banner',
    'id' => 'banner',
    'label' => 'Course Banner',
    'currentUrl' => null,
    'accept' => 'image/jpeg,image/png,image/webp',
    'maxSizeText' => 'JPG, PNG, or WEBP up to 2MB',
    'error' => null,
])

<div
    x-data="{
        previewUrl: null,
        removeBanner: false,
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.previewUrl = URL.createObjectURL(file);
                this.removeBanner = false;
            }
        },
        clearFile() {
            this.previewUrl = null;
            this.removeBanner = true;
            $refs.input.value = '';
        }
    }"
    class="space-y-2"
>
    <x-label :for="$id" :value="$label" />

    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <!-- Banner Preview Box (16:9) -->
        <div class="relative aspect-video w-full sm:w-56 shrink-0 overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center text-gray-400 shadow-inner">
            <template x-if="previewUrl">
                <img :src="previewUrl" alt="Banner Preview" class="h-full w-full object-cover" />
            </template>

            <template x-if="!previewUrl && '{{ $currentUrl }}' && !removeBanner">
                <img src="{{ $currentUrl }}" alt="Current Banner" class="h-full w-full object-cover" />
            </template>

            <template x-if="(!previewUrl && !'{{ $currentUrl }}') || (!previewUrl && removeBanner)">
                <div class="p-3 text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <span class="mt-1 block text-[11px] text-gray-400">No banner selected</span>
                </div>
            </template>
        </div>

        <!-- Input controls -->
        <div class="flex-1 space-y-2">
            <input
                x-ref="input"
                id="{{ $id }}"
                type="file"
                name="{{ $name }}"
                accept="{{ $accept }}"
                @change="handleFileSelect($event)"
                class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-gray-300 rounded-lg p-1.5"
            />
            <p class="text-[11px] text-gray-500">{{ $maxSizeText }}</p>

            @if($currentUrl)
                <div class="flex items-center gap-2 pt-1" x-show="!removeBanner">
                    <button
                        type="button"
                        @click="clearFile()"
                        class="text-xs text-rose-600 hover:text-rose-700 font-medium inline-flex items-center gap-1 hover:underline"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        Remove current banner
                    </button>
                    <input type="hidden" name="remove_banner" :value="removeBanner ? '1' : '0'">
                </div>
            @endif
        </div>
    </div>

    @if($error)
        <p class="text-xs text-red-600 mt-1">{{ $error }}</p>
    @endif
</div>
