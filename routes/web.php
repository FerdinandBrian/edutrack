<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardDosenController;
use App\Http\Controllers\DashboardMahasiswaController;

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