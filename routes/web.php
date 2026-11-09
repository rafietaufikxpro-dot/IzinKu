<?php

use App\Http\Controllers\IzinController;
use App\Http\Controllers\IzinVerifikasiController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Form & Pengajuan Izin Siswa (Task 3 & 5 / FR-04 & FR-06)
    Route::get('/izin/buat', [IzinController::class, 'create'])->name('izin.create');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');

    // Riwayat & detail izin untuk wali murid (Task 4)
    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/{izin}', [IzinController::class, 'show'])->name('izin.show');
    Route::get('/izin/{izin}/surat-sakit', [IzinController::class, 'downloadSuratSakit'])->name('izin.surat-sakit.download');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    // Verifikasi & ACC izin guru piket (Task 7 / FR-08 & FR-09)
    Route::get('/verifikasi-izin', [IzinVerifikasiController::class, 'index'])->name('izin-verifikasi.index');
    Route::get('/verifikasi-izin/{izin}', [IzinVerifikasiController::class, 'show'])->name('izin-verifikasi.show');
    Route::post('/verifikasi-izin/{izin}/acc', [IzinVerifikasiController::class, 'acc'])->name('izin-verifikasi.acc');
});

require __DIR__.'/auth.php';
