<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class AdminAdminController extends Controller
{
    public function index(Request $request)
    {
        $admins = Admin::orderBy('nama', 'asc')->get();

        return view('admin.admin.index', compact('admins'));
    }
}
