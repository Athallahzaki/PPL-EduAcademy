<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\Auth\RegisterController;
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
        Route::resource('users', Admin\UserController::class);
        Route::patch('users/{user}/status', [Admin\UserStatusController::class, 'update'])->name('users.status');
        Route::post('users/{user}/reset-password', Admin\UserPasswordResetController::class)->name('users.reset-password');
        Route::post('users/{id}/restore', [Admin\UserController::class, 'restore'])->name('users.restore');
    });

    // Instructor Area
    Route::prefix('instructor')->name('instructor.')->middleware('role:instructor')->group(function () {
        Route::get('/dashboard', Instructor\DashboardController::class)->name('dashboard');
    });

    // Student Area
    Route::prefix('student')->name('student.')->middleware('role:student')->group(function () {
        Route::get('/dashboard', Student\DashboardController::class)->name('dashboard');
    });
});
