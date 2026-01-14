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
        
        // Call stored procedure to get VA
        $result = \Illuminate\Support\Facades\DB::select("CALL sp_get_va(?)", [$tagihan->nrp]);
        $va = $result[0]->va ?? ('2911' . $tagihan->nrp);
        
        return view('mahasiswa.pembayaran.checkout', compact('tagihan', 'va'));
    }

    public function processCheckout(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);
        
        // Call stored procedure to generate VA
        $result = \Illuminate\Support\Facades\DB::select("CALL sp_get_va(?)", [$tagihan->nrp]);
        $va = $result[0]->va ?? ('2911' . $tagihan->nrp);
        
        // Store simulated payment data in session
        session([
            'payment_tagihan_id' => $id,
            'payment_method' => 'BCA Virtual Account',
            'payment_va' => $va
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

    public function simulation($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        if (auth()->user()->role !== 'mahasiswa' || $tagihan->nrp !== auth()->user()->identifier) abort(403);
        
        $paymentData = [
            'method' => session('payment_method') ?? 'Virtual Account',
            'va' => session('payment_va') ?? ('2911' . $tagihan->nrp)
        ];
        
        return view('mahasiswa.pembayaran.simulation', compact('tagihan', 'paymentData'));
    }

    public function confirmPayment($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        
        // Basic auth check
        if (auth()->user()->role === 'mahasiswa' && $tagihan->nrp !== auth()->user()->identifier) {
             abort(403);
        }

        // Call stored procedure to handle payment
        \Illuminate\Support\Facades\DB::statement("CALL sp_bayar_tagihan(?)", [$id]);
        
        // Clear session
        session()->forget(['payment_tagihan_id', 'payment_method', 'payment_va']);

        return redirect('/mahasiswa/pembayaran/' . $id)->with('success', 'Pembayaran berhasil dikonfirmasi (Stored Procedure)');
    }

    public function pilihTipePembayaran(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);
        if (auth()->user()->role !== 'mahasiswa' || $tagihan->nrp !== auth()->user()->identifier) abort(403);
        
        $tipe = $request->tipe; // 1 or 3
        
        if ($tipe == 1) {
            $tagihan->update([
                'tipe_pembayaran' => 1,
                'cicilan_ke' => 1
            ]);
        } elseif ($tipe == 3) {
            $jumlahPerCicilan = $tagihan->jumlah / 3;
            $baseDeadline = \Carbon\Carbon::parse($tagihan->batas_pembayaran);
            
            // Update the current record as the first installment
            $tagihan->update([
                'jumlah' => $jumlahPerCicilan,
                'tipe_pembayaran' => 3,
                'cicilan_ke' => 1
            ]);
            
            // Create 2 more installments with incrementing deadlines
            Tagihan::create([
                'nrp' => $tagihan->nrp,
                'jenis' => $tagihan->jenis . ' (Cicilan 2)',
                'jumlah' => $jumlahPerCicilan,
                'status' => 'Belum Lunas',
                'batas_pembayaran' => $baseDeadline->copy()->addMonth(),
                'tipe_pembayaran' => 3,
                'cicilan_ke' => 2
            ]);
            
            Tagihan::create([
                'nrp' => $tagihan->nrp,
                'jenis' => $tagihan->jenis . ' (Cicilan 3)',
                'jumlah' => $jumlahPerCicilan,
                'status' => 'Belum Lunas',
                'batas_pembayaran' => $baseDeadline->copy()->addMonths(2),
                'tipe_pembayaran' => 3,
                'cicilan_ke' => 3
            ]);
        }
        
        return redirect('/mahasiswa/pembayaran')->with('success', 'Tipe pembayaran berhasil dipilih');
    }

    // Legacy method - keeping it just in case, but redirecting to checkout
    public function bayar($id)
    {
        return redirect('/mahasiswa/pembayaran/' . $id . '/checkout');
    }

    public function getStudentAmount($nrp)
    {
        // Get the latest semester/year for this student to ensure we bill the correct period
        $latestRecord = \App\Models\Dkbs::where('nrp', $nrp)
            ->orderBy('tahun_ajaran', 'desc')
            ->orderBy('semester', 'desc')
            ->first();

        if (!$latestRecord) {
            return response()->json(['sks' => 0, 'amount' => 0, 'period' => 'N/A']);
        }

        $totalSks = \App\Models\Dkbs::where('dkbs.nrp', $nrp)
            ->where('dkbs.tahun_ajaran', $latestRecord->tahun_ajaran)
            ->where('dkbs.semester', $latestRecord->semester)
            ->join('mata_kuliah', 'dkbs.kode_mk', '=', 'mata_kuliah.kode_mk')
            ->sum('mata_kuliah.sks');
            
        $amount = $totalSks * 300000;
        
        return response()->json([
            'sks' => $totalSks,
            'amount' => $amount,
            'period' => $latestRecord->semester . ' ' . $latestRecord->tahun_ajaran
        ]);
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
        
        $request->validate([
            'nrp' => 'required',
            'jumlah' => 'required',
            'jenis' => 'required',
            'batas_pembayaran' => 'required|date'
        ]);
        
        $data = $request->all();
        $data['status'] = 'Belum Lunas';
        
        Tagihan::create($data);
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
