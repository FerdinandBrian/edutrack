<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DokumenController extends Controller
{
    public function index()
    {
        // In a real app, this might fetch generated docs from a database.
        // For now, we returns a static view as per requirements/plan.
        return view('mahasiswa.dokumen.index');
    }
}
