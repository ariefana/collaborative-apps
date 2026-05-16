<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DocumentController::class, 'index'])->name('dashboard');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document:slug}', [DocumentController::class, 'show'])->name('documents.show');
});

// Route untuk menyimpan auto-save
Route::put('/documents/{document:slug}', [DocumentController::class, 'update'])->name('documents.update');
// Route untuk mengembalikan (restore) versi history
Route::post('/documents/{document:slug}/restore/{revision}', [DocumentController::class, 'restore'])->name('documents.restore');

// Tambahkan baris ini di bawah route restore yang kemarin
Route::delete('/documents/{document:slug}/history', [DocumentController::class, 'deleteAllHistory'])->name('documents.history.destroy');

require __DIR__.'/auth.php';
