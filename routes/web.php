<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ResumeController::class, 'index'])->name('dashboard');

    Route::post('/resumes', [ResumeController::class, 'store'])->name('resumes.store');
    Route::put('/resumes/{resume}', [ResumeController::class, 'update'])->name('resumes.update');
    Route::delete('/resumes/{resume}', [ResumeController::class, 'destroy'])->name('resumes.destroy');
    Route::post('/resumes/{resume}/restore', [ResumeController::class, 'restore'])
        ->withTrashed()
        ->name('resumes.restore');
    Route::delete('/resumes/{resume}/force', [ResumeController::class, 'forceDestroy'])
        ->withTrashed()
        ->name('resumes.force-destroy');
    Route::post('/resumes/{resume}/duplicate', [ResumeController::class, 'duplicate'])->name('resumes.duplicate');
    Route::patch('/resumes/{resume}/status', [ResumeController::class, 'updateStatus'])->name('resumes.status');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
