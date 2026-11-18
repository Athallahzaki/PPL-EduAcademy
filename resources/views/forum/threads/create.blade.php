<x-layouts.app title="Buat Topik Diskusi Baru">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-xs text-gray-500 gap-2">
            <a href="{{ route('forum.threads.index') }}" class="hover:text-indigo-600 transition-colors">Forum Diskusi</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">Buat Topik Baru</span>
        </nav>

        <!-- Page Header -->
        <x-page-header
            title="Mulai Topik Diskusi Baru"
            description="Tanyakan pertanyaan, bagikan pengetahuan, atau diskusikan materi pelajaran dengan komunitas EduAcademy."
        />

        <!-- Form Card -->
        <x-card class="p-6 sm:p-8">
            <form method="POST" action="{{ route('forum.threads.store') }}" class="space-y-6">
                @csrf

                <!-- Subject Category -->
                <div>
                    <x-label for="subject_id" value="Kategori Mata Pelajaran" required />
                    <x-select id="subject_id" name="subject_id" class="mt-1" required>
                        <option value="">-- Pilih Kategori Pelajaran --</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </x-select>
                    <x-input-error :messages="$errors->get('subject_id')" class="mt-1" />
                </div>

                <!-- Title -->
                <div>
                    <x-label for="title" value="Judul Topik Diskusi" required />
                    <x-input
                        id="title"
                        name="title"
                        type="text"
                        class="mt-1 w-full"
                        placeholder="Contoh: Bagaimana cara memahami konsep async/await di JavaScript?"
                        value="{{ old('title') }}"
                        required
                        maxlength="255"
                    />
                    <p class="mt-1 text-xs text-gray-500">Tuliskan judul yang spesifik dan jelas (minimal 5 karakter).</p>
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <!-- Content -->
                <div>
                    <x-label for="content" value="Isi Pembahasan / Pertanyaan" required />
                    <x-textarea
                        id="content"
                        name="content"
                        rows="8"
                        class="mt-1 w-full"
                        placeholder="Jelaskan pertanyaan atau topik diskusi Anda secara rinci..."
                        required
                    >{{ old('content') }}</x-textarea>
                    <p class="mt-1 text-xs text-gray-500">Berikan konteks lengkap agar pengguna lain dapat memberikan jawaban yang membantu (minimal 10 karakter).</p>
                    <x-input-error :messages="$errors->get('content')" class="mt-1" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a
                        href="{{ route('forum.threads.index') }}"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Batal
                    </a>
                    <x-button type="submit" variant="primary">
                        Publikasikan Topik Diskusi
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
