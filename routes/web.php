<?php

use App\Http\Controllers\AiRewriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicResumeController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\ResumePreviewController;
use App\Http\Controllers\ResumeVersionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::get('/about', fn () => Inertia::render('About'))->name('about');
Route::get('/policy', fn () => Inertia::render('Policy'))->name('policy');
Route::get('/contacts', fn () => Inertia::render('Contacts'))->name('contacts');

Route::get('/resume/new', [ResumeController::class, 'create'])->name('resumes.create');

Route::post('/resume-preview', ResumePreviewController::class)
    ->middleware('throttle:30,1')
    ->name('resumes.preview');

Route::post('/ai/rewrite', AiRewriteController::class)
    ->middleware('throttle:10,1')
    ->name('ai.rewrite');

Route::get('/r/{resume:slug}', [PublicResumeController::class, 'show'])
    ->withTrashed()
    ->name('resumes.public');

Route::get('/r/{resume:slug}/file', [PublicResumeController::class, 'file'])
    ->withTrashed()
    ->name('resumes.public-file');

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
    Route::get('/resumes/{resume}/pdf', [ResumeController::class, 'pdf'])->name('resumes.pdf');
    Route::post('/resumes/{resume}/duplicate', [ResumeController::class, 'duplicate'])->name('resumes.duplicate');
    Route::post('/resumes/{resume}/archive', [ResumeController::class, 'archive'])->name('resumes.archive');
    Route::post('/resumes/{resume}/unarchive', [ResumeController::class, 'unarchive'])->name('resumes.unarchive');

    Route::get('/resumes/{resume}/versions', [ResumeVersionController::class, 'index'])->name('resumes.versions.index');
    Route::get('/resumes/{resume}/versions/{version}/pdf', [ResumeVersionController::class, 'pdf'])->name('resumes.versions.pdf');
    Route::post('/resumes/{resume}/versions/{version}/restore', [ResumeVersionController::class, 'restore'])->name('resumes.versions.restore');
    Route::delete('/resumes/{resume}/versions/{version}', [ResumeVersionController::class, 'destroy'])->name('resumes.versions.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
