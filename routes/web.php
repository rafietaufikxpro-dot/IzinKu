<?php

use App\Http\Controllers\IzinController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:wali_murid'])->group(function () {
    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/create', [IzinController::class, 'create'])->name('izin.create');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/izin/{izin}', [IzinController::class, 'show'])->name('izin.show');
    Route::get('/izin/{izin}/surat-sakit', [IzinController::class, 'downloadSuratSakit'])->name('izin.surat-sakit.download');
});

require __DIR__.'/auth.php';
