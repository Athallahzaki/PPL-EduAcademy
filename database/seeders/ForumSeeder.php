<?php

namespace Database\Seeders;

use App\Models\Comment;
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
        $thread1 = DiscussionThread::firstOrCreate(
            ['title' => 'Tips Memahami Arsitektur MVC dan Komponen Blade di Laravel 13'],
            [
                'user_id' => $instructor->id,
                'subject_id' => $programming?->id,
                'content' => "Halo rekan-rekan pembelajar!\n\nBagi yang baru memulai belajar framework Laravel, penting untuk memahami pemisahan tanggung jawab antara Model, View, dan Controller.\n\nBeberapa tips utama:\n1. Jaga controller tetap ramping (skinny controllers).\n2. Manfaatkan Eloquent Model Scopes dan Form Requests untuk validasi.\n3. Gunakan Blade Components untuk elemen antarmuka yang sering dipakai ulang.\n\nBagaimana pengalaman rekan-rekan sejauh ini dalam mengelola arsitektur kode?",
            ]
        );

        // Root comment by student (PBI-15)
        $comment1 = Comment::firstOrCreate(
            [
                'thread_id' => $thread1->id,
                'comment' => 'Terima kasih tipsnya Pak! Kapan sebaiknya kita menggunakan Anonymous Component vs Class-based Component di Blade?',
            ],
            [
                'user_id' => $student->id,
                'parent_id' => null,
            ]
        );

        // Nested reply by instructor (depth 1)
        $reply1 = Comment::firstOrCreate(
            [
                'thread_id' => $thread1->id,
                'parent_id' => $comment1->id,
            ],
            [
                'user_id' => $instructor->id,
                'comment' => 'Pertanyaan bagus! Untuk komponen tampilan murni tanpa logika query yang berat, Anonymous Component (file .blade.php di resources/views/components) jauh lebih cepat dan sederhana.',
            ]
        );

        // Nested reply to reply by student (depth 2)
        Comment::firstOrCreate(
            [
                'thread_id' => $thread1->id,
                'parent_id' => $reply1->id,
            ],
            [
                'user_id' => $student->id,
                'comment' => 'Paham Pak, terima kasih banyak atas penjelasannya!',
            ]
        );

        // 2. Thread 2: Design - UI/UX Inklusif
        $thread2 = DiscussionThread::firstOrCreate(
            ['title' => 'Rekomendasi Font dan Kontras Warna untuk Desain Aplikasi Belajar Inklusif'],
            [
                'user_id' => $student->id,
                'subject_id' => $design?->id,
                'content' => "Halo semua, saya sedang merancang antarmuka aplikasi edukasi agar ramah bagi pengguna dengan gangguan penglihatan (color blindness).\n\nAda saran kombinasi palet warna dan tingkat kontras minimum yang sesuai standar WCAG AAA?",
            ]
        );

        Comment::firstOrCreate(
            [
                'thread_id' => $thread2->id,
                'comment' => 'Gunakan rasio kontras minimal 4.5:1 untuk teks normal dan 3:1 untuk teks besar. Manfaatkan tools seperti WebAIM Contrast Checker untuk memverifikasi palet warna Anda.',
            ],
            [
                'user_id' => $instructor->id,
                'parent_id' => null,
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
