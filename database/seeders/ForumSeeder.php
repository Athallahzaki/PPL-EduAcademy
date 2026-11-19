<?php

namespace Database\Seeders;

use App\Models\DiscussionThread;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = User::where('email', 'student.active@eduacademy.test')->first()
            ?? User::where('role', 'student')->first();
        $instructor = User::where('email', 'instructor.active@eduacademy.test')->first()
            ?? User::where('role', 'instructor')->first();

        if (! $student || ! $instructor) {
            return;
        }

        $programming = Subject::where('slug', 'programming')->first() ?? Subject::first();
        $design = Subject::where('slug', 'design')->first() ?? Subject::first();
        $math = Subject::where('slug', 'mathematics')->first() ?? Subject::first();

        // 1. Thread 1: Programming - Tips MVC
        DiscussionThread::firstOrCreate(
            ['title' => 'Tips Memahami Arsitektur MVC dan Komponen Blade di Laravel 13'],
            [
                'user_id' => $instructor->id,
                'subject_id' => $programming?->id,
                'content' => "Halo rekan-rekan pembelajar!\n\nBagi yang baru memulai belajar framework Laravel, penting untuk memahami pemisahan tanggung jawab antara Model, View, dan Controller.\n\nBeberapa tips utama:\n1. Jaga controller tetap ramping (skinny controllers).\n2. Manfaatkan Eloquent Model Scopes dan Form Requests untuk validasi.\n3. Gunakan Blade Components untuk elemen antarmuka yang sering dipakai ulang.\n\nBagaimana pengalaman rekan-rekan sejauh ini dalam mengelola arsitektur kode?",
            ]
        );

        // 2. Thread 2: Design - UI/UX Inklusif
        DiscussionThread::firstOrCreate(
            ['title' => 'Rekomendasi Font dan Kontras Warna untuk Desain Aplikasi Belajar Inklusif'],
            [
                'user_id' => $student->id,
                'subject_id' => $design?->id,
                'content' => "Halo semua, saya sedang merancang antarmuka aplikasi edukasi agar ramah bagi pengguna dengan gangguan penglihatan (color blindness).\n\nAda saran kombinasi palet warna dan tingkat kontras minimum yang sesuai standar WCAG AAA?",
            ]
        );

        // 3. Thread 3: Mathematics - Algoritma dan Matematika Diskrit
        DiscussionThread::firstOrCreate(
            ['title' => 'Penerapan Teori Graf dan Matematika Diskrit pada Optimasi Jaringan'],
            [
                'user_id' => $instructor->id,
                'subject_id' => $math?->id,
                'content' => "Topik diskusi ini membahas implementasi algoritma Dijkstra dan Bellman-Ford dalam mencari rute terpendek.\n\nDi mana saja rekan-rekan pernah menemukan implementasi konsep ini pada studi kasus nyata selain Google Maps?",
            ]
        );
    }
}
