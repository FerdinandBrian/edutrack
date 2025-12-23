<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout']);

// sementara dashboard dummy
Route::get('/admin/dashboard', fn () => 'ADMIN OK');
Route::get('/dosen/dashboard', fn () => 'DOSEN OK');
Route::get('/mahasiswa/dashboard', fn () => 'MAHASISWA OK');
