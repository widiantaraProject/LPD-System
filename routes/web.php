<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Dashboard;
use App\Livewire\Nasabah;
use App\Livewire\Simpanan;
use App\Livewire\TransaksiSimpanan;
use App\Livewire\Pinjaman;
use App\Livewire\Angsuran;
use App\Livewire\Pengaturan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Nasabah
    Route::get('/nasabah', Nasabah::class)->name('nasabah');

    // Simpanan
    Route::get('/simpanan', Simpanan::class)->name('simpanan');

    // Transaksi Simpanan
    Route::get('/transaksi-simpanan', TransaksiSimpanan::class)->name('transaksi-simpanan');

    // Pinjaman
    Route::get('/pinjaman', Pinjaman::class)->name('pinjaman');

    // Angsuran
    Route::get('/angsuran', Angsuran::class)->name('angsuran');

    // Pengaturan
    Route::get('/pengaturan', Pengaturan::class)->name('pengaturan');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
