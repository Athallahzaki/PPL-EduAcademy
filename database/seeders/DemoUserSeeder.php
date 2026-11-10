<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $password = Hash::make('password');

        // 1. Instructor, active and onboarded (with instructor_profiles)
        $activeInstructor = User::updateOrCreate(
            ['email' => 'instructor.active@eduacademy.test'],
            [
                'name' => 'Prof. Sarah Jenkins',
                'password' => $password,
                'role' => Role::Instructor,
                'status' => AccountStatus::Active,
                'must_change_password' => false,
                'onboarded_at' => now(),
            ]
        );

        $activeInstructor->instructorProfile()->updateOrCreate(
            ['user_id' => $activeInstructor->id],
            [
                'bio' => 'Senior Lecturer with 10+ years teaching Computer Science and Full-Stack Engineering.',
                'expertise' => 'Web Development & Cloud Architecture',
                'cv_path' => 'instructor-cvs/demo-sarah-jenkins-cv.pdf',
                'portfolio_url' => 'https://sarahjenkins.dev',
            ]
        );

        // 2. Instructor, pending
        User::updateOrCreate(
            ['email' => 'instructor.pending@eduacademy.test'],
            [
                'name' => 'Dr. Michael Chen',
                'password' => $password,
                'role' => Role::Instructor,
                'status' => AccountStatus::Pending,
                'must_change_password' => false,
                'onboarded_at' => null,
            ]
        );

        // 3. Instructor, rejected
        User::updateOrCreate(
            ['email' => 'instructor.rejected@eduacademy.test'],
            [
                'name' => 'David Miller',
                'password' => $password,
                'role' => Role::Instructor,
                'status' => AccountStatus::Rejected,
                'must_change_password' => false,
                'onboarded_at' => null,
            ]
        );

        // 4. Student, active and onboarded
        User::updateOrCreate(
            ['email' => 'student.active@eduacademy.test'],
            [
                'name' => 'Alex Rivera',
                'password' => $password,
                'role' => Role::Student,
                'status' => AccountStatus::Active,
                'must_change_password' => false,
                'onboarded_at' => now(),
                'education_level' => 'university',
            ]
        );

        // 5. Student, active but not onboarded
        User::updateOrCreate(
            ['email' => 'student.unonboarded@eduacademy.test'],
            [
                'name' => 'Emma Watson',
                'password' => $password,
                'role' => Role::Student,
                'status' => AccountStatus::Active,
                'must_change_password' => false,
                'onboarded_at' => null,
            ]
        );

        // 6. Student, suspended
        User::updateOrCreate(
            ['email' => 'student.suspended@eduacademy.test'],
            [
                'name' => 'Tom Reynolds',
                'password' => $password,
                'role' => Role::Student,
                'status' => AccountStatus::Suspended,
                'must_change_password' => false,
                'onboarded_at' => now(),
                'education_level' => 'senior_high',
            ]
        );
    }
}

