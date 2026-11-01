<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PasswordResetOtpController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// Login form is accessible whether or not the user is already signed in.
// If they are signed in, the controller will log them out first so they
// can switch accounts without manually clicking "Logout".
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset with 6-digit OTP
Route::get('/forgot-password', [PasswordResetOtpController::class, 'showRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetOtpController::class, 'sendOtp'])->name('password.otp.send');
Route::get('/reset-password-otp', [PasswordResetOtpController::class, 'showResetForm'])->name('password.otp.reset');
Route::post('/reset-password-otp', [PasswordResetOtpController::class, 'resetPassword'])->name('password.otp.update');

// The auth middleware verifies identity. Each controller's Policy then
// checks the specific resource/action, so one admin role cannot cross modules.
Route::middleware('auth')->group(function () {
    Route::resource('students', StudentController::class);

    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::match(['put', 'patch'], '/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // Super Admin Sub-Admin & Role Management
    Route::middleware('can:manage-roles')->group(function () {
        Route::get('/admin/roles', [AdminRoleController::class, 'index'])->name('admin.roles.index');
        Route::post('/admin/roles', [AdminRoleController::class, 'store'])->name('admin.roles.store');
        Route::put('/admin/roles/{user}', [AdminRoleController::class, 'update'])->name('admin.roles.update');
    });
});

Route::get('/', function () {
    return auth()->check()
        ? redirect()->to(auth()->user()->landingPath())
        : redirect()->route('about.index');
})->name('home');

Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
