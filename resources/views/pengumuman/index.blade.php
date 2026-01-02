@extends(auth()->user()->role == 'dosen' ? 'layouts.dosen' : (auth()->user()->role == 'mahasiswa' ? 'layouts.mahasiswa' : 'layouts.admin'))

@section('title', 'Pengumuman')

@section('content')

<!-- HERO SECTION -->
<div class="mb-8 bg-white rounded-2xl p-8 border border-slate-100 shadow-sm overflow-hidden relative">
    <div class="relative z-10">
        <h2 class="text-2xl font-bold text-slate-800 text-center lg:text-left">Pusat Pengumuman & Agenda</h2>
        <p class="text-slate-500 mt-2 max-w-2xl text-center lg:text-left">
            Dapatkan informasi terbaru mengenai kegiatan akademik, hari libur, dan berbagai agenda kampus lainnya di sini.
        </p>
    </div>
    <!-- Decorative element -->
    <div class="absolute -right-10 -top-10 w-40 h-40 bg-amber-50 rounded-full blur-3xl opacity-50"></div>
    <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- LEFT: Calendar & Widgets -->
    <div class="space-y-8">
        <!-- Calendar Widget -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <span class="font-bold text-lg text-slate-800">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</span>
                <div class="flex space-x-1">
                    <a href="?month={{ $month == 1 ? 12 : $month - 1 }}&year={{ $month == 1 ? $year - 1 : $year }}" 
                       class="p-2 rounded-lg hover:bg-slate-100 text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <a href="?month={{ $month == 12 ? 1 : $month + 1 }}&year={{ $month == 12 ? $year + 1 : $year }}" 
                       class="p-2 rounded-lg hover:bg-slate-100 text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            
            <div class="grid grid-cols-7 gap-1 text-center mb-4">
                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $day }}</div>
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
                        $date = \Carbon\Carbon::createFromDate($year, $month, $day)->startOfDay();
                        $eventsOnDay = $events->filter(function($e) use ($date) {
                            return $date->between($e->waktu_mulai, $e->waktu_selesai ?? $e->waktu_mulai);
                        });
                        $hasEvent = $eventsOnDay->isNotEmpty();
                        $isToday = $date->isSameDay($today);

                        $bgClass = 'text-slate-600 hover:bg-slate-50 transition-colors cursor-default';
                        $dotClass = '';

                        if ($isToday) {
                            $bgClass = 'bg-blue-600 text-white font-bold shadow-md shadow-blue-200';
                        } elseif ($hasEvent) {
                            if ($eventsOnDay->contains('kategori', 'libur')) {
                                $bgClass = 'bg-red-50 text-red-600 font-bold';
                                $dotClass = 'bg-red-500';
                            } elseif ($eventsOnDay->contains('kategori', 'akademik')) {
                                $bgClass = 'bg-blue-50 text-blue-600 font-bold';
                                $dotClass = 'bg-blue-500';
                            } else {
                                $bgClass = 'bg-emerald-50 text-emerald-600 font-bold';
                                $dotClass = 'bg-emerald-500';
                            }
                        }
                    @endphp
                    <div class="relative h-9 w-9 flex items-center justify-center rounded-xl text-sm mx-auto {{ $bgClass }}">
                        {{ $day }}
                        @if($hasEvent && !$isToday)
                            <div class="absolute bottom-1 w-1 h-1 rounded-full {{ $dotClass }}"></div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <!-- Filter/Legend -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-bold text-slate-800 mb-4 text-sm uppercase tracking-wider">Kategori Agenda</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3 text-sm text-slate-600">
                    <span class="w-4 h-4 rounded-lg bg-blue-500 shadow-sm shadow-blue-200"></span> 
                    <span class="font-medium">Akademik</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-slate-600">
                    <span class="w-4 h-4 rounded-lg bg-red-500 shadow-sm shadow-red-200"></span> 
                    <span class="font-medium">Libur Nasional</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-slate-600">
                    <span class="w-4 h-4 rounded-lg bg-emerald-500 shadow-sm shadow-emerald-200"></span> 
                    <span class="font-medium">Kegiatan / Event</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Announcement List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/30 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Daftar Pengumuman</h2>
                    <p class="text-xs text-slate-500 mt-1">Menampilkan semua informasi terbaru</p>
                </div>
                
                @if(auth()->user()->role === 'admin')
                <a href="/admin/pengumuman/create" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-sm shadow-blue-200 text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Baru
                </a>
                @endif
            </div>
            
            <div class="divide-y divide-slate-50">
                @forelse($list as $item)
                <div class="p-8 hover:bg-slate-50/50 transition duration-300 group">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest 
                                    {{ $item->kategori == 'libur' ? 'bg-red-50 text-red-600 ring-1 ring-red-100' : ($item->kategori == 'akademik' ? 'bg-blue-50 text-blue-600 ring-1 ring-blue-100' : 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100') }}">
                                    {{ $item->kategori }}
                                </span>
                                <div class="flex items-center gap-1.5 text-xs text-slate-400 font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $item->waktu_mulai->translatedFormat('d M Y') }}
                                    @if($item->waktu_selesai && !$item->waktu_selesai->isSameDay($item->waktu_mulai))
                                        <span class="mx-1 text-slate-300">→</span>
                                        {{ $item->waktu_selesai->translatedFormat('d M Y') }}
                                    @endif
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 mb-3 group-hover:text-blue-600 transition-colors">{{ $item->judul }}</h3>
                            <div class="text-slate-600 text-sm leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100/50">{{ $item->isi }}</div>
                        </div>
                        
                        @if(auth()->user()->role === 'admin')
                        <div class="flex flex-col gap-2 ml-6 transition-opacity">
                            <a href="/admin/pengumuman/{{ $item->id }}/edit" class="p-2.5 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all border border-blue-100 bg-blue-50 shadow-sm" title="Edit Pengumuman">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <form action="/admin/pengumuman/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full p-2.5 text-red-600 hover:bg-red-600 hover:text-white rounded-xl transition-all border border-red-100 bg-red-50 shadow-sm" title="Hapus Pengumuman">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-20 text-center text-slate-400">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <p class="font-medium">Belum ada pengumuman untuk ditampilkan.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
