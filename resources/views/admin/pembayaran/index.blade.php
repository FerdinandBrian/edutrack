@extends('layouts.admin')

@section('title','Pembayaran')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Pembayaran</h2>
            <a href="/admin/pembayaran/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Buat Tagihan</a>
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-slate-500 border-b bg-slate-50/50">
                    <th class="py-4 px-4 w-12">No</th>
                    <th>NRP</th>
                    <th>Nama</th>
                    <th class="text-indigo-600">Virtual Account</th>
                    <th>Jumlah</th>
                    <th>Deadline</th>
                    <th>Tipe</th>
                    <th class="text-center">Status</th>
                    <th class="text-right px-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php 
                    $currentNrp = null; 
                    $studentCount = 0;
                @endphp
                @forelse($data as $i => $row)
                    @php
                        $isNewStudent = ($row->nrp !== $currentNrp);
                        if ($isNewStudent) {
                            $studentCount++;
                            $currentNrp = $row->nrp;
                        }
                    @endphp

                    {{-- PEMBATAS PER MAHASISWA --}}
                    @if($isNewStudent)
                        <tr class="bg-indigo-50/40">
                            <td colspan="9" class="py-4 px-6 border-t border-indigo-100 shadow-inner">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-[10px] font-bold shadow-sm">
                                        {{ $studentCount }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-[0.2em] leading-none mb-1">Data Pembayaran Mahasiswa</span>
                                        <span class="text-xs font-black text-indigo-900 uppercase tracking-wider">{{ optional($row->mahasiswa)->nama }}</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif

                <tr class="hover:bg-slate-50/80 transition-all text-sm group">
                    <td class="py-5 px-4 text-slate-300 font-mono text-center text-xs group-hover:text-slate-500 transition-colors border-l-2 border-transparent group-hover:border-indigo-500">
                        @if($isNewStudent)
                            #{{ $studentCount }}
                        @endif
                    </td>
                    <td class="py-5 font-semibold text-slate-700 tracking-tight">{{ $row->nrp }}</td>
                    <td class="py-5 text-slate-600 font-medium">{{ optional($row->mahasiswa)->nama }}</td>
                    <td class="py-5">
                        <div class="flex items-center">
                            <span class="font-mono font-bold text-indigo-700 bg-indigo-100/50 px-3 py-1.5 rounded-lg border border-indigo-200/50 shadow-sm">{{ $va = '2911' . $row->nrp }}</span>
                        </div>
                    </td>
                    <td class="font-semibold text-slate-800">Rp {{ number_format($row->jumlah, 0, ',', '.') }}</td>
                    <td class="text-slate-500">{{ $row->batas_pembayaran ? \Carbon\Carbon::parse($row->batas_pembayaran)->format('d/m/y') : '-' }}</td>
                    <td>
                        @if($row->tipe_pembayaran == 3)
                            <span class="text-[10px] bg-orange-100 text-orange-700 px-2 py-1 rounded-md font-bold uppercase">3x (Ke-{{ $row->cicilan_ke }})</span>
                        @elseif($row->tipe_pembayaran == 1)
                            <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-1 rounded-md font-bold uppercase">1x</span>
                        @else
                            <span class="text-[10px] text-slate-400 italic bg-slate-100 px-2 py-1 rounded">Belum Pilih</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @php
                            $status = strtolower($row->status ?? 'belum lunas');
                            $badgeClass = match($status) {
                                'lunas' => 'bg-emerald-600 text-white shadow-sm shadow-emerald-200',
                                'pending', 'belum lunas' => 'bg-red-600 text-white shadow-sm shadow-red-200',
                                default => 'bg-slate-400 text-white',
                            };
                        @endphp
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $badgeClass }}">
                            {{ $row->status ?? 'Belum Lunas' }}
                        </span>
                    </td>
                    <td class="text-right px-4 whitespace-nowrap">
                        <div class="flex items-center justify-end gap-3 py-1">
                            <a href="/admin/pembayaran/{{ $row->id }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs uppercase tracking-tighter transition-all">Lihat</a>
                            <a href="/admin/pembayaran/{{ $row->id }}/edit" class="text-amber-600 hover:text-amber-900 font-bold text-xs uppercase tracking-tighter transition-all">Edit</a>
                            <form action="/admin/pembayaran/{{ $row->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-900 font-bold text-xs uppercase tracking-tighter transition-all">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-20 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-slate-400 font-medium italic">Belum ada data pembayaran.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
