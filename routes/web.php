<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::get('/about', fn () => Inertia::render('About'))->name('about');
Route::get('/policy', fn () => Inertia::render('Policy'))->name('policy');
Route::get('/contacts', fn () => Inertia::render('Contacts'))->name('contacts');

Route::get('/resume/new', [ResumeController::class, 'create'])->name('resumes.create');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ResumeController::class, 'index'])->name('dashboard');
    Route::get('/trash', [ResumeController::class, 'trash'])->name('resumes.trash');

    Route::post('/resumes', [ResumeController::class, 'store'])->name('resumes.store');
    Route::get('/resumes/{resume}/edit', [ResumeController::class, 'edit'])->name('resumes.edit');
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
