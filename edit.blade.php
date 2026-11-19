<x-layouts.app title="Edit Profil Instruktur">
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <a href="{{ route('instructor.profile.show') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">&larr; Kembali ke Profil</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900">Edit Profil & Spesialisasi</h1>
            <p class="mt-1 text-sm text-gray-500">Lengkapi informasi profesional agar calon siswa mengenal pengalaman dan keahlian Anda.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <p class="font-semibold">Periksa kembali data yang dimasukkan:</p>
                <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            @method('PUT')

            <div>
                <label for="bio" class="mb-2 block text-sm font-semibold text-gray-700">Deskripsi / Tentang Saya</label>
                <textarea id="bio" name="bio" rows="5" maxlength="1000" required placeholder="Ceritakan latar belakang, pengalaman mengajar, dan pendekatan pembelajaran Anda..." class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('bio', $profile?->bio) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Maksimal 1.000 karakter.</p>
            </div>

            <div>
                <label for="expertise" class="mb-2 block text-sm font-semibold text-gray-700">Keahlian & Spesialisasi</label>
                <input id="expertise" name="expertise" type="text" maxlength="255" required value="{{ old('expertise', $profile?->expertise) }}" placeholder="Contoh: UI/UX Design, Laravel, Database, Data Analytics" class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500">Pisahkan beberapa keahlian dengan koma.</p>
            </div>

            <div>
                <label for="portfolio_url" class="mb-2 block text-sm font-semibold text-gray-700">URL Portofolio</label>
                <input id="portfolio_url" name="portfolio_url" type="url" maxlength="255" value="{{ old('portfolio_url', $profile?->portfolio_url) }}" placeholder="https://portofolio-anda.com" class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500">Masukkan tautan website, GitHub, Behance, atau portofolio lainnya.</p>
            </div>

            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-5">
                <label for="cv" class="block text-sm font-semibold text-gray-700">CV (Curriculum Vitae)</label>
                @if ($profile?->cv_path)
                    <p class="mt-2 text-xs text-emerald-700">CV sudah tersimpan. Unggah file baru jika ingin menggantinya.</p>
                @else
                    <p class="mt-2 text-xs text-gray-500">Belum ada CV. Silakan unggah CV dalam format PDF.</p>
                @endif
                <input id="cv" name="cv" type="file" accept="application/pdf,.pdf" class="mt-3 block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-2 text-xs text-gray-500">Format PDF, ukuran maksimal 2 MB. Kosongkan jika tidak ingin mengganti CV.</p>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('instructor.profile.show') }}" class="inline-flex justify-center rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-layouts.app>
