@extends(auth()->user()->role == 'dosen' ? 'layouts.dosen' : (auth()->user()->role == 'mahasiswa' ? 'layouts.mahasiswa' : 'layouts.admin'))

@section('title', 'Pengumuman')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- LEFT: Calendar & Widgets -->
    <div class="space-y-6">
        <!-- Calendar Widget -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <span class="font-bold text-lg text-slate-800">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</span>
                <div class="flex space-x-2">
                    <a href="?month={{ $month == 1 ? 12 : $month - 1 }}&year={{ $month == 1 ? $year - 1 : $year }}" 
                       class="p-1 rounded hover:bg-slate-100 text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <a href="?month={{ $month == 12 ? 1 : $month + 1 }}&year={{ $month == 12 ? $year + 1 : $year }}" 
                       class="p-1 rounded hover:bg-slate-100 text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            
            <div class="grid grid-cols-7 gap-1 text-center mb-2">
                @foreach(['M', 'S', 'S', 'R', 'K', 'J', 'S'] as $day)
                    <div class="text-xs font-semibold text-slate-400">{{ $day }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-1 text-center">
                @php
                    $startDay = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth()->dayOfWeek; 
                    $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;
                    $today = \Carbon\Carbon::now();
                @endphp

                @for($i = 0; $i < $startDay; $i++)
                    <div></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $date = \Carbon\Carbon::createFromDate($year, $month, $day);
                        $eventsOnDay = $events->filter(function($e) use ($date) {
                            return $date->between($e->waktu_mulai, $e->waktu_selesai ?? $e->waktu_mulai);
                        });
                        $hasEvent = $eventsOnDay->isNotEmpty();
                        $isToday = $date->isSameDay($today);

                        $bgClass = 'text-slate-600 hover:bg-slate-50';
                        $dotClass = '';

                        if ($isToday) {
                            $bgClass = 'bg-blue-600 text-white font-bold';
                        } elseif ($hasEvent) {
                            if ($eventsOnDay->contains('kategori', 'libur')) {
                                $bgClass = 'bg-red-100 text-red-700 font-semibold';
                                $dotClass = 'bg-red-500';
                            } elseif ($eventsOnDay->contains('kategori', 'akademik')) {
                                $bgClass = 'bg-blue-100 text-blue-700 font-semibold';
                                $dotClass = 'bg-blue-500';
                            } else {
                                $bgClass = 'bg-green-100 text-green-700 font-semibold';
                                $dotClass = 'bg-green-500';
                            }
                        }
                    @endphp
                    <div class="relative h-8 w-8 flex items-center justify-center rounded-full text-sm mx-auto {{ $bgClass }}">
                        {{ $day }}
                        @if($hasEvent && !$isToday)
                            <div class="absolute bottom-0.5 w-1 h-1 rounded-full {{ $dotClass }}"></div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <!-- Filter/Legend -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-slate-800 mb-3">Kategori</h3>
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span> Akademik
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span> Libur
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span> Kegiatan
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Announcement List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-800">Daftar Pengumuman</h2>
                
                @if(auth()->user()->role === 'admin')
                <a href="/admin/pengumuman/create" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Pengumuman
                </a>
                @endif
            </div>
            
            <div class="divide-y divide-slate-100">
                @forelse($list as $item)
                <div class="p-6 hover:bg-slate-50 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide 
                                    {{ $item->kategori == 'libur' ? 'bg-red-100 text-red-600' : ($item->kategori == 'akademik' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600') }}">
                                    {{ $item->kategori }}
                                </span>
                                <span class="text-xs text-slate-400">
                                    {{ $item->waktu_mulai->translatedFormat('d F Y') }}
                                    @if($item->waktu_selesai && !$item->waktu_selesai->isSameDay($item->waktu_mulai))
                                        - {{ $item->waktu_selesai->translatedFormat('d F Y') }}
                                    @endif
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-800 mb-2">{{ $item->judul }}</h3>
                            <div class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ $item->isi }}</div>
                        </div>
                        
                        @if(auth()->user()->role === 'admin')
                        <div class="flex items-center gap-2 ml-4">
                            <a href="/admin/pengumuman/{{ $item->id }}/edit" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded bg-white border border-slate-200 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <form action="/admin/pengumuman/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded bg-white border border-slate-200 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    <p>Belum ada pengumuman.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
