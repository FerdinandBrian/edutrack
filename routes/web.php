<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardDosenController;
use App\Http\Controllers\DashboardMahasiswaController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\DkbsController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\PerkuliahanController;
use App\Http\Controllers\PengumumanController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
// ... other routes (keeping logic)
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    return match ($role) {
        'admin'     => redirect('/admin/dashboard'),
        'dosen'     => redirect('/dosen/dashboard'),
        'mahasiswa' => redirect('/mahasiswa/dashboard'),
        default => abort(403),
    };
})->middleware('auth');

// Global Auth Middleware
Route::middleware('auth')->group(function () {

    // ============================
    // ADMIN ROUTES (Strict)
    // ============================
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/profile', [ProfileController::class, 'index']);
        Route::get('/profile/edit', [ProfileController::class, 'edit']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::get('/dashboard', [DashboardAdminController::class, 'index']);
        
        // User Management
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/create', [UserController::class, 'create']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}/edit', [UserController::class, 'edit']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        
        // DKBS Admin
        Route::get('/dkbs', [DkbsController::class, 'index']);
        Route::get('/dkbs/student/{nrp}', [DkbsController::class, 'showStudent']);
        Route::get('/dkbs/create', [DkbsController::class, 'create']);
        Route::post('/dkbs', [DkbsController::class, 'store']);
        Route::get('/dkbs/{dkbs}/edit', [DkbsController::class, 'edit']);
        Route::put('/dkbs/{dkbs}', [DkbsController::class, 'update']);
        Route::delete('/dkbs/{dkbs}', [DkbsController::class, 'destroy']);
        
        // Perkuliahan (Jadwal Kelas)
        Route::resource('perkuliahan', PerkuliahanController::class);

        // API
        Route::get('/api/mata-kuliah/{semester}', [DkbsController::class, 'getMataKuliahBySemester']);
        Route::get('/api/mata-kuliah-by-jurusan', [PerkuliahanController::class, 'getMataKuliahByJurusan']);
        Route::get('/api/perkuliahan-by-ta', [DkbsController::class, 'getPerkuliahanByTahunAjaran']);
        Route::get('/api/perkuliahan-by-ta-jurusan', [DkbsController::class, 'getPerkuliahanByTaAndJurusan']);
        Route::get('/api/dosen-by-jurusan', [PerkuliahanController::class, 'getDosenByJurusan']);

        Route::get('/api/student-amount/{nrp}', [TagihanController::class, 'getStudentAmount']);
        
        // Pembayaran Admin
        Route::get('/pembayaran', [TagihanController::class, 'index']);
        Route::get('/pembayaran/create', [TagihanController::class, 'create']);
        Route::post('/pembayaran', [TagihanController::class, 'store']);
        Route::get('/pembayaran/{id}', [TagihanController::class, 'show']);
        Route::get('/pembayaran/{tagihan}/edit', [TagihanController::class, 'edit']);
        Route::put('/pembayaran/{tagihan}', [TagihanController::class, 'update']);
        Route::delete('/pembayaran/{tagihan}', [TagihanController::class, 'destroy']);

        // Pengumuman Admin
        Route::get('/pengumuman', [PengumumanController::class, 'index']);
        Route::get('/pengumuman/create', [PengumumanController::class, 'create']);
        Route::post('/pengumuman', [PengumumanController::class, 'store']);
        Route::get('/pengumuman/{pengumuman}/edit', [PengumumanController::class, 'edit']);
        Route::put('/pengumuman/{pengumuman}', [PengumumanController::class, 'update']);
        Route::delete('/pengumuman/{pengumuman}', [PengumumanController::class, 'destroy']);

        // Manajemen Mata Kuliah (CRUD)
        Route::get('/mata-kuliah', [MataKuliahController::class, 'index']);
        Route::get('/mata-kuliah/create', [MataKuliahController::class, 'create']);
        Route::post('/mata-kuliah', [MataKuliahController::class, 'store']);
        Route::get('/mata-kuliah/{id}/edit', [MataKuliahController::class, 'edit']);
        Route::put('/mata-kuliah/{id}', [MataKuliahController::class, 'update']);
        Route::delete('/mata-kuliah/{id}', [MataKuliahController::class, 'destroy']);
        Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index']);
    });

    // ============================
    // DOSEN ROUTES (Strict)
    // ============================
    Route::middleware('role:dosen')->prefix('dosen')->group(function () {
        Route::get('/profile', [ProfileController::class, 'index']);
        Route::get('/profile/edit', [ProfileController::class, 'edit']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::get('/dashboard', [DashboardDosenController::class, 'index']);
        
        Route::get('/pengumuman', [PengumumanController::class, 'index']);

        // Nilai Dosen
        Route::get('/nilai', [NilaiController::class, 'index']);
        Route::get('/nilai/create', [NilaiController::class, 'create']);
        Route::post('/nilai', [NilaiController::class, 'store']);
        Route::get('/nilai/{id}', [NilaiController::class, 'show']);
        Route::get('/nilai/{nilai}/edit', [NilaiController::class, 'edit']);
        Route::put('/nilai/{nilai}', [NilaiController::class, 'update']);
        Route::get('/nilai/kelas/{id}', [NilaiController::class, 'showClass']);
        Route::delete('/nilai/{nilai}', [NilaiController::class, 'destroy']);

        // Jadwal Dosen
        Route::get('/jadwal', [JadwalController::class, 'index']);
        Route::get('/jadwal/create', [JadwalController::class, 'create']);
        Route::post('/jadwal', [JadwalController::class, 'store']);
        Route::get('/jadwal/{id}', [JadwalController::class, 'show']);
        Route::get('/jadwal/{jadwal}/edit', [JadwalController::class, 'edit']);
        Route::put('/jadwal/{jadwal}', [JadwalController::class, 'update']);
        Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy']);

        // Presensi Dosen
        Route::get('/presensi', [PresensiController::class, 'index']);
        Route::get('/presensi/create', [PresensiController::class, 'create']);
        Route::post('/presensi', [PresensiController::class, 'store']);
        Route::get('/presensi/{presensi}', [PresensiController::class, 'show']);
        Route::get('/presensi/{presensi}/edit', [PresensiController::class, 'edit']);
        Route::put('/presensi/{presensi}', [PresensiController::class, 'update']);
        Route::get('/presensi/kelas/{id}', [PresensiController::class, 'showClass']);
        Route::delete('/presensi/{presensi}', [PresensiController::class, 'destroy']);
    });

    // ============================
    // MAHASISWA ROUTES (Strict)
    // ============================
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->group(function () {
        Route::get('/profile', [ProfileController::class, 'index']);
        Route::get('/profile/edit', [ProfileController::class, 'edit']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::get('/dashboard', [DashboardMahasiswaController::class, 'index']);
        
        Route::get('/pengumuman', [PengumumanController::class, 'index']);
        
        Route::get('/dkbs', [DkbsController::class, 'index']);
        Route::get('/nilai', [NilaiController::class, 'index']);
        Route::get('/nilai/{id}', [NilaiController::class, 'show']);
        Route::get('/jadwal', [JadwalController::class, 'index']);
        Route::get('/jadwal/{id}', [JadwalController::class, 'show']);
        Route::get('/pembayaran', [TagihanController::class, 'index']);
        Route::get('/pembayaran/{id}', [TagihanController::class, 'show']);
        Route::get('/presensi', [PresensiController::class, 'index']);
        Route::get('/presensi/{presensi}', [PresensiController::class, 'show']);
        
        // Payment Flow
        Route::get('/pembayaran/{id}/checkout', [TagihanController::class, 'checkout']);
        Route::post('/pembayaran/{id}/checkout', [TagihanController::class, 'processCheckout']);
        Route::get('/pembayaran/{id}/instruction', [TagihanController::class, 'instruction']);
        Route::post('/pembayaran/{id}/confirm', [TagihanController::class, 'confirmPayment']);
        
        Route::post('/pembayaran/{id}/pilih-tipe', [TagihanController::class, 'pilihTipePembayaran']);
        Route::post('/pembayaran/{id}/bayar', [TagihanController::class, 'bayar']); // Legacy/Quick Pay if needed
        
        Route::get('/dokumen', [DokumenController::class, 'index']);
        
        Route::get('/mata-kuliah', function(){
            $user = auth()->user();
            $data = \App\Models\Dkbs::with('mataKuliah')
                ->where('nrp', $user->identifier)
                ->get();
            return view('mahasiswa.mata_kuliah.index', compact('data'));
        });
    });
});