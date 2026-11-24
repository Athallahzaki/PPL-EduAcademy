<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\ScholarshipStatus;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Database\Seeder;

class ScholarshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', Role::Admin->value)->first();

        if (! $admin) {
            $admin = User::factory()->create([
                'name' => 'Admin EduAcademy',
                'email' => 'admin@eduacademy.test',
                'role' => Role::Admin,
            ]);
        }

        $scholarships = [
            [
                'title' => 'Beasiswa Talenta Digital AI 2026 (Track Machine Learning)',
                'description' => 'Program beasiswa pelatihan intensif kecerdasan buatan dan machine learning bekerjasama dengan mitra teknologi global untuk mencetak talenta AI unggul di Indonesia.',
                'requirements' => "1. Siswa SMA/SMK sederajat kelas akhir atau Mahasiswa aktif D3/D4/S1 jurusan STEM.\n2. Memiliki pemahaman dasar pemrograman (Python lebih disukai).\n3. Berkomitmen mengikuti program penuh waktu selama 16 minggu.\n4. Mengunggah KTP/Kartu Pelajar, CV, dan Transkrip Nilai / Rapor terakhir.",
                'quota' => 150,
                'start_date' => now()->subDays(10)->toDateString(),
                'deadline' => now()->addDays(45)->toDateString(),
                'status' => ScholarshipStatus::Published,
            ],
            [
                'title' => 'SDG 4 Tech Equality Grant (Inklusi Putri di Teknologi)',
                'description' => 'Bantuan beasiswa pendidikan dan pelatihan karir teknologi khusus perempuan muda guna mendukung SDG 4: Akses Pendidikan Berkualitas & Inklusif untuk Semua.',
                'requirements' => "1. Warga Negara Indonesia berjenis kelamin perempuan.\n2. Terdaftar sebagai pelajar aktif atau lulusan baru dalam 1 tahun terakhir.\n3. Mengunggah esai motivasi bertema 'Teknologi untuk Perubahan Sosial' (maksimal 500 kata).\n4. Melampirkan surat rekomendasi dari guru atau dosen pembimbing.",
                'quota' => 75,
                'start_date' => now()->subDays(5)->toDateString(),
                'deadline' => now()->addDays(25)->toDateString(),
                'status' => ScholarshipStatus::Published,
            ],
            [
                'title' => 'Fullstack Developer Cohort 4 (Track Modern Web & Cloud)',
                'description' => 'Beasiswa bootcamp komprehensif Laravel, React, dan Cloud DevOps dengan jaminan penyaluran kerja magang di startup dan enterprise.',
                'requirements' => "1. Berusia 18 - 25 tahun.\n2. Memiliki dasar logika pemrograman dan algoritma.\n3. Memiliki laptop dengan spesifikasi minimal RAM 8 GB.\n4. Siap menandatangani komitmen penyelesaian proyek akhir.",
                'quota' => 100,
                'start_date' => now()->subDays(15)->toDateString(),
                'deadline' => now()->addDays(12)->toDateString(),
                'status' => ScholarshipStatus::Published,
            ],
            [
                'title' => 'Beasiswa Afirmasi Daerah 3T (Infrastruktur Jaringan & Komputasi Cloud)',
                'description' => 'Bantuan pendidikan penuh biaya sertifikasi internasional jaringan dan cloud bagi pelajar yang berdomisili di wilayah 3T (Terdepan, Terluar, Tertinggal).',
                'requirements' => "1. WNI berdomisili tetap di kabupaten/kota berkategori 3T (dibuktikan dengan KTP/Kartu Keluarga).\n2. Memiliki minat kuat dalam bidang infrastruktur IT dan sistem jaringan.\n3. Mengunggah surat keterangan domisili resmi dari pemerintah desa/kelurahan.",
                'quota' => 50,
                'start_date' => now()->subDays(20)->toDateString(),
                'deadline' => now()->addDays(30)->toDateString(),
                'status' => ScholarshipStatus::Published,
            ],
            [
                'title' => 'Desain Produk & UI/UX Accessibility Fellowship 2026',
                'description' => 'Program beasiswa desain produk digital inklusif dengan fokus pada aksesibilitas web (WCAG 2.1) dan user experience modern.',
                'requirements' => "1. Portofolio desain minimal 2 studi kasus (tautan Behance/Dribbble/Figma/PDF).\n2. Memahami prinsip dasar User Centered Design.\n3. Lulus tes studi kasus awal dari tim seleksi EduAcademy.",
                'quota' => 40,
                'start_date' => now()->subDays(30)->toDateString(),
                'deadline' => now()->subDays(2)->toDateString(),
                'status' => ScholarshipStatus::Closed,
            ],
            [
                'title' => 'Beasiswa Perintis Sains & Matematika Terapan (Draf)',
                'description' => 'Program beasiswa penelitian dan olimpiade bagi talenta muda sains dan matematika yang sedang dalam persiapan rilis.',
                'requirements' => "1. Memiliki piagam penghargaan atau nilai rapor matematika/sains minimal 85.\n2. Rekomendasi dari kepala sekolah.",
                'quota' => 25,
                'start_date' => now()->addDays(7)->toDateString(),
                'deadline' => now()->addDays(60)->toDateString(),
                'status' => ScholarshipStatus::Draft,
            ],
        ];

        foreach ($scholarships as $data) {
            Scholarship::firstOrCreate(
                ['title' => $data['title']],
                [
                    ...$data,
                    'admin_id' => $admin->id,
                ]
            );
        }
    }
}
