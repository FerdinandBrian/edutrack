@extends('layouts.admin')

@section('title', 'Detail Pembayaran: ' . $mahasiswa->nama)

@section('content')
<div class="mb-6">
    <a href="/admin/pembayaran" class="text-slate-500 hover:text-indigo-600 transition-colors flex items-center gap-2 text-sm font-medium group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Daftar Mahasiswa
    </a>
</div>

<div class="flex flex-col gap-6">
    {{-- Student Profile Info (Horizontal Layout) --}}
    <div class="w-full">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="flex flex-col md:flex-row items-stretch">
                {{-- Left Side: Profile Banner --}}
                <div class="md:w-1/3 bg-gradient-to-br from-indigo-600 to-violet-700 p-8 text-white relative flex flex-col items-center justify-center text-center overflow-hidden">
                    <div class="relative z-10">
                        <div class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-2xl mx-auto flex items-center justify-center mb-4 shadow-xl border border-white/30">
                            <span class="text-3xl font-bold">{{ substr($mahasiswa->nama, 0, 1) }}</span>
                        </div>
                        <h2 class="text-2xl font-bold tracking-tight mb-1">{{ $mahasiswa->nama }}</h2>
                        <p class="text-indigo-100/80 text-sm font-medium">{{ $mahasiswa->nrp }}</p>
                    </div>
                    {{-- Decorative Circle --}}
                    <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                </div>

                {{-- Right Side: Profile Details --}}
                <div class="md:w-2/3 p-8 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex flex-wrap gap-8 items-center">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Program Studi</label>
                            <p class="text-slate-700 font-bold text-lg leading-tight">{{ $mahasiswa->jurusan ?? '-' }}</p>
                        </div>
                        <div class="h-10 w-px bg-slate-100 hidden md:block"></div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Virtual Account</label>
                            <div class="flex items-center gap-2">
                                <span class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-xl font-mono font-bold border border-indigo-100 text-lg shadow-sm">2911{{ $mahasiswa->nrp }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-auto">
                        <a href="/admin/pembayaran/create?nrp={{ $mahasiswa->nrp }}" 
                           class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg shadow-indigo-100 transition-all group whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Tagihan Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Lists --}}
    <div class="w-full">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-slate-800">Riwayat Tagihan & Pembayaran</h3>
                <span class="bg-indigo-100 text-indigo-700 text-[10px] font-black px-2 py-1 rounded-full uppercase tracking-wider">{{ $data->count() }} Record(s)</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-[11px] uppercase font-bold tracking-widest text-slate-400 border-b border-slate-100">
                            <th class="py-4 px-6 min-w-[220px]">Jenis Tagihan</th>
                            <th class="py-4 px-4 text-center">Tipe</th>
                            <th class="py-4 px-4">Jumlah</th>
                            <th class="py-4 px-4">Deadline</th>
                            <th class="py-4 px-4 text-center">Status</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($data as $row)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="py-5 px-6">
                                <span class="font-bold text-slate-700 block transition-colors group-hover:text-indigo-600 truncate max-w-[200px]" title="{{ $row->jenis }}">{{ $row->jenis }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5 whitespace-nowrap">Dibuat pada {{ $row->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="py-5 px-4 text-center whitespace-nowrap">
                                @if($row->tipe_pembayaran == 3)
                                    <span class="inline-block text-[10px] bg-orange-100 text-orange-700 px-2.5 py-1 rounded-md font-black uppercase ring-1 ring-orange-200 whitespace-nowrap">Cicil 3x (Ke-{{ $row->cicilan_ke }})</span>
                                @elseif($row->tipe_pembayaran == 1)
                                    <span class="inline-block text-[10px] bg-blue-100 text-blue-700 px-2.5 py-1 rounded-md font-black uppercase ring-1 ring-blue-200">Lunas 1x</span>
                                @else
                                    <span class="inline-block text-[10px] text-slate-400 bg-slate-100 px-2.5 py-1 rounded font-bold uppercase tracking-tighter">Default</span>
                                @endif
                            </td>
                            <td class="py-5 px-4 whitespace-nowrap">
                                <span class="font-bold text-slate-800">Rp {{ number_format($row->jumlah, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-5 px-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium {{ \Carbon\Carbon::parse($row->batas_pembayaran)->isPast() && $row->status != 'Lunas' ? 'text-rose-600' : 'text-slate-600' }}">
                                        {{ \Carbon\Carbon::parse($row->batas_pembayaran)->format('d M Y') }}
                                    </span>
                                    @if(\Carbon\Carbon::parse($row->batas_pembayaran)->isPast() && $row->status != 'Lunas')
                                        <span class="text-[9px] font-bold text-rose-500 uppercase tracking-tighter">Terlewat</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-5 px-4 text-center whitespace-nowrap">
                                @php
                                    $status = strtolower($row->status ?? 'belum lunas');
                                    $badgeClass = match($status) {
                                        'lunas' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
                                        'pending' => 'bg-amber-100 text-amber-700 ring-amber-200',
                                        default => 'bg-rose-100 text-rose-700 ring-rose-200',
                                    };
                                @endphp
                                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase ring-1 {{ $badgeClass }} whitespace-nowrap">
                                    {{ $row->status ?? 'Belum Lunas' }}
                                </span>
                            </td>
                            <td class="py-5 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="/admin/pembayaran/{{ $row->id }}/edit" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit Tagihan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="/admin/pembayaran/{{ $row->id }}" method="POST" onsubmit="return confirm('Hapus tagihan ini? Data tidak dapat dikembalikan.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Hapus Tagihan">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-slate-400 font-medium italic">Belum ada data tagihan untuk mahasiswa ini.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
