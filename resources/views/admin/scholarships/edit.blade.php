<x-layouts.app title="Ubah Info Beasiswa">
    <div class="mx-auto max-w-4xl space-y-6">
        <x-page-header title="Ubah Info Beasiswa" description="Perbarui rincian informasi program, syarat pendaftaran, atau kuota beasiswa." />

        <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-xs sm:p-8">
            <form method="POST" action="{{ route('admin.scholarships.update', $scholarship) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="space-y-4">
                    <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-2">Informasi Program</h2>

                    <div>
                        <x-label for="title" value="Judul Beasiswa" required />
                        <x-input
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title', $scholarship->title) }}"
                            required
                            autofocus
                        />
                        <x-input-error :messages="$errors->get('title')" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-label for="quota" value="Kuota Penerima (Orang)" required />
                            <x-input
                                id="quota"
                                name="quota"
                                type="number"
                                min="1"
                                value="{{ old('quota', $scholarship->quota) }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('quota')" />
                        </div>

                        <div>
                            <x-label for="status" value="Status Publikasi" required />
                            <x-select id="status" name="status" required>
                                <option value="published" @selected(old('status', $scholarship->status->value) === 'published')>Dipublikasikan</option>
                                <option value="draft" @selected(old('status', $scholarship->status->value) === 'draft')>Draft</option>
                                <option value="closed" @selected(old('status', $scholarship->status->value) === 'closed')>Ditutup</option>
                            </x-select>
                            <x-input-error :messages="$errors->get('status')" />
                        </div>
                    </div>

                    <div>
                        <x-label for="description" value="Ringkasan / Deskripsi Singkat Beasiswa" />
                        <x-textarea
                            id="description"
                            name="description"
                            rows="3"
                        >{{ old('description', $scholarship->description) }}</x-textarea>
                        <x-input-error :messages="$errors->get('description')" />
                    </div>
                </div>

                <!-- Schedule & Deadlines -->
                <div class="space-y-4 pt-2">
                    <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-2">Jadwal & Periode Pendaftaran</h2>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-label for="start_date" value="Tanggal Mulai Pendaftaran (Opsional)" />
                            <x-input
                                id="start_date"
                                name="start_date"
                                type="date"
                                value="{{ old('start_date', $scholarship->start_date?->format('Y-m-d')) }}"
                            />
                            <x-input-error :messages="$errors->get('start_date')" />
                        </div>

                        <div>
                            <x-label for="deadline" value="Tenggat Waktu / Batas Akhir Pendaftaran" required />
                            <x-input
                                id="deadline"
                                name="deadline"
                                type="date"
                                value="{{ old('deadline', $scholarship->deadline->format('Y-m-d')) }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('deadline')" />
                        </div>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="space-y-4 pt-2">
                    <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-2">Kriteria Persyaratan & Berkas</h2>

                    <div>
                        <x-label for="requirements" value="Syarat Pendaftaran & Dokumen yang Dibutuhkan" required />
                        <x-textarea
                            id="requirements"
                            name="requirements"
                            rows="6"
                            required
                        >{{ old('requirements', $scholarship->requirements) }}</x-textarea>
                        <x-input-error :messages="$errors->get('requirements')" />
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <a
                        href="{{ route('admin.scholarships.show', $scholarship) }}"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-2xs transition hover:bg-gray-50 hover:text-gray-900"
                    >
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Batal</span>
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
