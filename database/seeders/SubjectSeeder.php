<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Note for PBI-14 (Forum): This seeder runs across all environments and populates
     * the canonical subjects table. Use subjects.id as subject_id instead of free-text.
     */
    public function run(): void
    {
        $subjects = [
            'Programming',
            'Design',
            'Mathematics',
            'Science',
            'Languages',
            'Business',
            'Other',
        ];

        foreach ($subjects as $name) {
            Subject::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}

