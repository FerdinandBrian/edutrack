<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $admin = $user->admin; // relasi

        return view('admin.dashboard', [
            'user' => $user,
            'admin' => $admin,
        ]);
    }
}