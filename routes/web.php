<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CourseReviewController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Forum;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Instructor;
use App\Http\Controllers\Student;
use Illuminate\Support\Facades\Route;

// Public entry point
Route::get('/', HomeController::class)->name('home');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login');
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:register');
});

// Logout (POST only)
Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

// Authenticated but pending setup (exempt from password.changed / onboarded)
Route::middleware(['auth', 'account.active'])->group(function () {
    Route::get('/password/change', [ChangePasswordController::class, 'show'])->name('password.change');
    Route::put('/password/change', [ChangePasswordController::class, 'update'])->name('password.update');
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

// Protected Role Areas
Route::middleware(['auth', 'account.active', 'password.changed', 'onboarded'])->group(function () {
    // Admin Area
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', Admin\DashboardController::class)->name('dashboard');
        Route::resource('announcements', AnnouncementController::class)->except('show');
        Route::resource('scholarships', Admin\ScholarshipController::class);
        Route::post('scholarships/{id}/restore', [Admin\ScholarshipController::class, 'restore'])->name('scholarships.restore');
        Route::resource('users', Admin\UserController::class);
        Route::patch('users/{user}/status', [Admin\UserStatusController::class, 'update'])->name('users.status');
        Route::post('users/{user}/reset-password', Admin\UserPasswordResetController::class)->name('users.reset-password');
        Route::post('users/{id}/restore', [Admin\UserController::class, 'restore'])->name('users.restore');
        Route::get('forums', [Forum\ThreadController::class, 'index'])->name('forums.index');
        Route::get('reviews', [CourseReviewController::class, 'adminIndex'])->name('reviews.index');
        Route::delete('reviews/{review}', [CourseReviewController::class, 'adminDestroy'])->name('reviews.destroy');
    });

    // Instructor Area (PBI-11)
    Route::prefix('instructor')->name('instructor.')->middleware('role:instructor')->group(function () {
        Route::get('/dashboard', Instructor\DashboardController::class)->name('dashboard');
        Route::get('/profile', [Instructor\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [Instructor\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [Instructor\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/cv', [Instructor\ProfileController::class, 'downloadCv'])->name('profile.cv');
        Route::resource('courses', Instructor\CourseController::class);
        Route::patch('courses/{course}/status', [Instructor\CourseStatusController::class, 'update'])->name('courses.status');
        Route::get('courses/{course}/students', [Instructor\CourseStudentController::class, 'index'])->name('courses.students');
        Route::get('courses/{course}/reviews', [CourseReviewController::class, 'instructorIndex'])->name('courses.reviews.index');

        // Curriculum Management (PBI-12)
        Route::resource('courses.modules', Instructor\ModuleController::class)->except(['show'])->scoped();
        Route::patch('courses/{course}/modules/{module}/move', [Instructor\ModuleController::class, 'move'])->name('courses.modules.move');
        Route::patch('courses/{course}/modules/{module}/publish', [Instructor\ModuleController::class, 'publish'])->name('courses.modules.publish');

        Route::resource('courses.modules.chapters', Instructor\ChapterController::class)->scoped();
        Route::patch('courses/{course}/modules/{module}/chapters/{chapter}/move', [Instructor\ChapterController::class, 'move'])->name('courses.modules.chapters.move');
        Route::patch('courses/{course}/modules/{module}/chapters/{chapter}/publish', [Instructor\ChapterController::class, 'publish'])->name('courses.modules.chapters.publish');

        Route::get('forums', [Forum\ThreadController::class, 'index'])->name('forums.index');
    });

    // Student Area (PBI-11)
    Route::prefix('student')->name('student.')->middleware('role:student')->group(function () {
        Route::get('/dashboard', Student\DashboardController::class)->name('dashboard');
        Route::get('catalog', [Student\CatalogController::class, 'index'])->name('catalog.index');
        Route::get('catalog/{course}', [Student\CatalogController::class, 'show'])->name('catalog.show');
        Route::post('courses/{course}/enroll', [Student\EnrollmentController::class, 'store'])->name('courses.enroll');
        Route::get('courses', [Student\MyCourseController::class, 'index'])->name('courses.index');
        Route::get('courses/{course}', [Student\MyCourseController::class, 'show'])->name('courses.show');
        Route::post('courses/{course}/reviews', [CourseReviewController::class, 'store'])->name('courses.reviews.store');
        Route::put('reviews/{review}', [CourseReviewController::class, 'update'])->name('reviews.update');
        Route::delete('reviews/{review}', [CourseReviewController::class, 'destroy'])->name('reviews.destroy');

        // Course Content & Reader (PBI-12)
        Route::get('courses/{course}/content', [Student\CourseContentController::class, 'index'])->name('courses.modules.index');
        Route::get('courses/{course}/modules/{module}/chapters/{chapter}', [Student\CourseContentController::class, 'chapter'])
            ->scopeBindings()->name('courses.modules.chapters.show');

        Route::get('forums', [Forum\ThreadController::class, 'index'])->name('forums.index');
    });

    // Forum Community Area (PBI-14: Thread/Topik Diskusi)
    Route::prefix('forum')->name('forum.')->group(function () {
        Route::resource('threads', Forum\ThreadController::class);
    });
});
