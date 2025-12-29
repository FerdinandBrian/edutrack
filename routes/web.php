<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardDosenController;
use App\Http\Controllers\DashboardMahasiswaController;
use App\Http\Controllers\PresensiController;

// Feature routes (Nilai, Jadwal, DKBS, Pembayaran) — wired to controllers for full CRUD
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\DkbsController;
use App\Http\Controllers\TagihanController;

Route::middleware('auth')->group(function () {
    // Nilai
    Route::get('/nilai', [NilaiController::class, 'index']);
    Route::get('/nilai/create', [NilaiController::class, 'create'])->middleware('role:2');
    Route::post('/nilai', [NilaiController::class, 'store'])->middleware('role:2');
    Route::get('/nilai/{id}', [NilaiController::class, 'show']);
    Route::get('/nilai/{nilai}/edit', [NilaiController::class, 'edit'])->middleware('role:2');
    Route::put('/nilai/{nilai}', [NilaiController::class, 'update'])->middleware('role:2');
    Route::delete('/nilai/{nilai}', [NilaiController::class, 'destroy'])->middleware('role:2');

    // Jadwal
    Route::get('/jadwal', [JadwalController::class, 'index']);
    Route::get('/jadwal/create', [JadwalController::class, 'create'])->middleware('role:2');
    Route::post('/jadwal', [JadwalController::class, 'store'])->middleware('role:2');
    Route::get('/jadwal/{id}', [JadwalController::class, 'show']);
    Route::get('/jadwal/{jadwal}/edit', [JadwalController::class, 'edit'])->middleware('role:2');
    Route::put('/jadwal/{jadwal}', [JadwalController::class, 'update'])->middleware('role:2');
    Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy'])->middleware('role:2');

    // DKBS
    Route::get('/dkbs', [DkbsController::class, 'index']);
    Route::get('/dkbs/create', [DkbsController::class, 'create'])->middleware('role:1');
    Route::post('/dkbs', [DkbsController::class, 'store'])->middleware('role:1');
    Route::get('/dkbs/{dkbs}/edit', [DkbsController::class, 'edit'])->middleware('role:1');
    Route::put('/dkbs/{dkbs}', [DkbsController::class, 'update'])->middleware('role:1');
    Route::delete('/dkbs/{dkbs}', [DkbsController::class, 'destroy'])->middleware('role:1');

    // Pembayaran (Tagihan)
    Route::get('/pembayaran', [TagihanController::class, 'index']);
    Route::get('/pembayaran/create', [TagihanController::class, 'create'])->middleware('role:1');
    Route::post('/pembayaran', [TagihanController::class, 'store'])->middleware('role:1');
    Route::get('/pembayaran/{id}', [TagihanController::class, 'show']);
    Route::get('/pembayaran/{tagihan}/edit', [TagihanController::class, 'edit'])->middleware('role:1');
    Route::put('/pembayaran/{tagihan}', [TagihanController::class, 'update'])->middleware('role:1');
    Route::delete('/pembayaran/{tagihan}', [TagihanController::class, 'destroy'])->middleware('role:1');
});

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->middleware('guest');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth');

// Presensi routes (viewable by authenticated users; create/update/delete by Dosen)
Route::middleware('auth')->group(function () {
    Route::get('/presensi', [PresensiController::class, 'index']);
    Route::get('/presensi/{presensi}', [PresensiController::class, 'show']);

    Route::middleware('role:2')->group(function () {
        Route::get('/presensi/create', [PresensiController::class, 'create']);
        Route::post('/presensi', [PresensiController::class, 'store']);
        Route::get('/presensi/{presensi}/edit', [PresensiController::class, 'edit']);
        Route::put('/presensi/{presensi}', [PresensiController::class, 'update']);
        Route::delete('/presensi/{presensi}', [PresensiController::class, 'destroy']);
    });
});

// Mata kuliah (list distinct kode_mk from jadwal)
Route::middleware('auth')->get('/mata-kuliah', function(){
    try {
        $data = \App\Models\Jadwal::select('kode_mk')->distinct()->get()->pluck('kode_mk');
    } catch (\Exception $e) {
        $data = collect();
    }
    return view('mata_kuliah.index', compact('data'));
});

Route::get('/dashboard', function () {

    $role = auth()->user()->id_role;

    return match ($role) {
        1 => redirect('/dashboard/admin'),
        2 => redirect('/dashboard/dosen'),
        3 => redirect('/dashboard/mahasiswa'),
        default => abort(403),
    };

})->middleware('auth');


Route::middleware(['auth', 'role:1'])->get(
    '/dashboard/admin',
    [DashboardAdminController::class, 'index']
);

Route::middleware(['auth', 'role:2'])->get(
    '/dashboard/dosen',
    [DashboardDosenController::class, 'index']
);

Route::middleware(['auth', 'role:3'])->get(
    '/dashboard/mahasiswa',
    [DashboardMahasiswaController::class, 'index']
);