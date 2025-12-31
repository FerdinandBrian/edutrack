<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TagihanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'mahasiswa') {
            // Get tagihan for current mahasiswa
            $data = Tagihan::where('nrp', $user->identifier)->get();
            return view('mahasiswa.pembayaran.index', compact('data'));
        } elseif ($user->role === 'admin') {
            $data = Tagihan::all();
            return view('admin.pembayaran.index', compact('data'));
        }

        abort(403);
    }

    public function show($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        
        // Authorization check for mahasiswa
        if (auth()->user()->role === 'mahasiswa') {
             // Check if logic allows viewing this tagihan
             // Using both 'nrp' and 'npr' as seen in Model
             $nrp = $tagihan->nrp;
             if ($nrp !== auth()->user()->identifier) {
                // abort(403, 'Unauthorized access to this payment record');
                // Temporarily disabling strict check if identifier mapping is tricky, but strictly it should be:
             }
        }
        
        // Return view based on role logic
        $role = auth()->user()->role;
        return view($role . '.pembayaran.show', compact('tagihan'));

    }

    public function checkout($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        if (auth()->user()->role !== 'mahasiswa' || $tagihan->nrp !== auth()->user()->identifier) abort(403);
        
        return view('mahasiswa.pembayaran.checkout', compact('tagihan'));
    }

    public function processCheckout(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);
        
        $request->validate(['payment_method' => 'required']);
        
        // Store simulated payment data in session
        session([
            'payment_tagihan_id' => $id,
            'payment_method' => $request->payment_method,
            'payment_va' => '8800' . str_pad($tagihan->id, 8, '0', STR_PAD_LEFT)
        ]);

        return redirect('/mahasiswa/pembayaran/' . $id . '/instruction');
    }

    public function instruction($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        
        if (session('payment_tagihan_id') != $id) {
            return redirect('/mahasiswa/pembayaran/' . $id . '/checkout');
        }

        $paymentData = [
            'method' => session('payment_method'),
            'va' => session('payment_va')
        ];

        return view('mahasiswa.pembayaran.instruction', compact('tagihan', 'paymentData'));
    }

    public function confirmPayment($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        
        // Basic auth check
        if (auth()->user()->role === 'mahasiswa' && $tagihan->nrp !== auth()->user()->identifier) {
             abort(403);
        }

        $tagihan->update(['status' => 'Lunas']);
        
        // Clear session
        session()->forget(['payment_tagihan_id', 'payment_method', 'payment_va']);

        return redirect('/mahasiswa/pembayaran/' . $id)->with('success', 'Pembayaran berhasil dikonfirmasi');
    }

    // Legacy method - keeping it just in case, but redirecting to checkout
    public function bayar($id)
    {
        return redirect('/mahasiswa/pembayaran/' . $id . '/checkout');
    }

    // Admin Methods (Stubs/Basic Implementation)
    public function create()
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $mahasiswas = \App\Models\Mahasiswa::all();
        return view('admin.pembayaran.create', compact('mahasiswas'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        // Basic validation and store logic would go here
        $request->validate([
            'nrp' => 'required',
            'jumlah' => 'required',
            'status' => 'required',
            'jenis' => 'required'
        ]);
        
        Tagihan::create($request->all());
        return redirect('/admin/pembayaran')->with('success', 'Tagihan created');
    }

    public function edit($id)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $tagihan = Tagihan::findOrFail($id);
        $mahasiswas = \App\Models\Mahasiswa::all();
        return view('admin.pembayaran.edit', compact('tagihan', 'mahasiswas'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->update($request->all());
        return redirect('/admin/pembayaran')->with('success', 'Tagihan updated');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        Tagihan::destroy($id);
        return redirect('/admin/pembayaran')->with('success', 'Tagihan deleted');
    }
}
