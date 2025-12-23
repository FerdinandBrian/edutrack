<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    echo "LOGIN ROUTE MASUK";
    exit;
});

/*
|--------------------------------------------------------------------------
| ROUTE LOGIN
|--------------------------------------------------------------------------
*/

// tampilkan form login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// proses login
Route::post('/login', [AuthController::class, 'login']);

// logout
Route::post('/logout', [AuthController::class, 'logout']);


/*
|--------------------------------------------------------------------------
| ROUTE DASHBOARD (BERDASARKAN ROLE)
|--------------------------------------------------------------------------
*/

// ADMIN (role = 1)
Route::middleware(['auth','role:1'])->get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

// DOSEN (role = 2)
Route::middleware(['auth','role:2'])->get('/dosen/dashboard', function () {
    return view('dosen.dashboard');
});

// MAHASISWA (role = 3)
Route::middleware(['auth','role:3'])->get('/mahasiswa/dashboard', function () {
    return view('mahasiswa.dashboard');
});


/*
|--------------------------------------------------------------------------
| DEFAULT REDIRECT
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});
