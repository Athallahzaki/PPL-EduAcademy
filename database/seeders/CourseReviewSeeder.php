<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseReviewSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::query()
            ->where('role', Role::Student->value)
            ->orderBy('id')
            ->get();
        $courses = Course::query()->orderBy('id')->get();

        if ($students->isEmpty() || $courses->isEmpty()) {
            return;
        }

        $samples = [
            [5, 'Materinya runtut dan mudah diikuti. Saya jadi lebih percaya diri mengerjakan proyek sendiri.'],
            [4, 'Penjelasan instruktur jelas dan contoh praktiknya sangat membantu memahami konsep.'],
            [5, 'Kelas yang sangat bermanfaat, terutama bagian latihan yang bisa langsung dicoba.'],
            [4, 'Materi disusun dengan baik. Akan lebih lengkap lagi jika ada beberapa latihan tambahan.'],
            [5, 'Saya suka cara penyampaian materinya, ringkas tetapi tetap mudah dipahami.'],
            [4, 'Banyak wawasan baru yang saya dapatkan dan materinya relevan untuk kebutuhan sekarang.'],
            [5, 'Pengalaman belajar yang menyenangkan. Setiap topik dijelaskan dengan contoh yang jelas.'],
            [4, 'Kualitas kelasnya bagus dan membantu saya memahami dasar-dasar topik ini.'],
        ];

        $created = 0;
        foreach ($courses as $course) {
            foreach ($students as $student) {
                if ($created >= count($samples)) {
                    break 2;
                }

                Enrollment::firstOrCreate([
                    'course_id' => $course->id,
                    'student_id' => $student->id,
                ]);

                [$rating, $review] = $samples[$created];
                CourseReview::firstOrCreate(
                    [
                        'course_id' => $course->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'rating' => $rating,
                        'review' => $review,
                    ]
                );
                $created++;
            }
        }
    }
}
