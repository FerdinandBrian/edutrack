@extends('layouts.mahasiswa')

@section('title','Riwayat Pembayaran')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-100 bg-white flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Riwayat Pembayaran</h2>
            <p class="text-sm text-slate-500 mt-1">Daftar tagihan dan status pembayaran semester</p>
        </div>
        <!-- Optional: Summary Stat -->
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-[11px] uppercase tracking-widest font-bold border-b border-slate-100">
                    <th class="px-8 py-4 w-16">#</th>
                    <th class="px-8 py-4">Keterangan Tagihan</th>
                    <th class="px-8 py-4">Nominal</th>
                    <th class="px-8 py-4 text-center">Status</th>
                    <th class="px-8 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($data as $i => $row)
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-8 py-5 text-sm text-slate-400 font-mono">{{ $i + 1 }}</td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800">{{ $row->jenis }}</span>
                            <!-- Assuming we can display date created if available, else just nrp for debugging or nothing -->
                            <span class="text-[10px] text-slate-400 font-mono mt-0.5">{{ optional($row->created_at)->format('d M Y') ?? 'Semester Ganjil 2024' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-sm font-medium text-slate-600">Rp {{ number_format($row->jumlah, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        @php
                            $status = $row->status ?? 'pending';
                            $statusClass = match(strtolower($status)) {
                                'lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                default => 'bg-slate-50 text-slate-600 border-slate-100',
                            };
                        @endphp
                        <div class="inline-block px-3 py-1 rounded-lg border {{ $statusClass }}">
                            <span class="text-xs font-bold uppercase tracking-wide">{{ $row->status }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-right">
                        @if(strtolower($row->status) == 'lunas')
                            <button disabled class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg text-xs font-bold uppercase tracking-wide cursor-not-allowed">
                                Lunas
                            </button>
                        @else
                            <a href="/mahasiswa/pembayaran/{{ $row->id }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase tracking-wide hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200">
                                Bayar
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <p class="text-slate-500 font-medium">Belum ada data tagihan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
