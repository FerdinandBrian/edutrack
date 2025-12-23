<?php

namespace App\Http\Controllers;

class TagihanController extends Controller
{
    public function index()
    {
        $tagihan = Tagihan::where('npr', auth()->user()->npr)->get();
        return view('tagihan.index', compact('tagihan'));
    }
}
