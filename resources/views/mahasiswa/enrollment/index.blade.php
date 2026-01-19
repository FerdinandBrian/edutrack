@extends('layouts.mahasiswa')

@section('title', 'Pendaftaran Kelas (DKBS)')

@section('content')
<div class="max-w-7xl mx-auto pb-24 px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb & Badge -->
    <div class="flex items-center gap-2 mb-4 animate-in fade-in duration-700">
        <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Akademik</span>
        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Pendaftaran Kelas</span>
    </div>

    <!-- Page Title & Hero Section -->
    <div class="relative mb-10 overflow-hidden rounded-3xl bg-slate-900 px-8 py-10 shadow-2xl animate-in slide-in-from-bottom-4 duration-700">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="space-y-2">
                <h1 class="text-4xl font-black text-white tracking-tight">Pendaftaran Kelas</h1>
                <p class="text-slate-400 max-w-lg leading-relaxed">
                    Selamat datang di portal pendaftaran kelas semester <strong>{{ $currentSemester }}</strong>. 
                    Silakan pilih kelas yang sesuai dengan minat dan jadwal Anda.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl px-6 py-4 border border-white/20 min-w-[200px]">
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-1">Periode Aktif</p>
                    <p class="text-white font-bold text-lg">{{ $activeTa }}</p>
                </div>
                <div class="bg-indigo-600 rounded-2xl px-6 py-4 shadow-xl min-w-[160px] relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <p class="text-[10px] uppercase font-bold text-indigo-200 tracking-widest mb-1">Total SKS</p>
                    <p class="text-white font-black text-3xl" id="total-sks-display">{{ $totalSks }}</p>
                </div>
            </div>
        </div>
        
        <!-- Background Elements -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 rounded-2xl flex items-center gap-4 animate-in fade-in zoom-in duration-300">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <p class="font-bold">Berhasil!</p>
                <p class="text-sm opacity-90">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-8 p-5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3 mb-3 text-rose-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-lg font-black italic tracking-tight underline decoration-rose-200">Mohon perbaiki kesalahan berikut:</p>
            </div>
            <ul class="space-y-2">
                @foreach ($errors->all() as $error)
                    <li class="flex items-center gap-2 text-sm font-medium">
                        <div class="w-1.5 h-1.5 rounded-full bg-rose-400"></div>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($isEnrolled)
        <!-- Locked State / Summary View -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden animate-in fade-in slide-in-from-bottom-8 duration-1000">
            <div class="bg-slate-50 border-b border-slate-200 p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center shadow-inner">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Pendaftaran Terkunci</h2>
                        <p class="text-slate-500 font-medium tracking-tight">Anda telah berhasil mendaftar kelas untuk periode {{ $activeTa }}.</p>
                    </div>
                </div>
                <a href="/mahasiswa/dkbs" class="inline-flex items-center gap-2 bg-white px-6 py-3 rounded-xl border border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition-all shadow-sm">
                    <span>Lihat DKBS Digital</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
            <div class="p-8">
                <div class="overflow-hidden rounded-2xl border border-slate-100 shadow-sm">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-widest text-slate-400 font-black">
                                <th class="px-6 py-4">Mata Kuliah</th>
                                <th class="px-6 py-4">Kelas</th>
                                <th class="px-6 py-4">Jadwal</th>
                                <th class="px-6 py-4">Ruangan</th>
                                <th class="px-6 py-4">SKS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($enrolledClasses as $class)
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-5">
                                        <p class="font-bold text-slate-800">{{ $class->mataKuliah->nama_mk ?? 'N/A' }}</p>
                                        <p class="text-xs font-mono text-slate-400 tracking-tighter">{{ $class->kode_mk }}</p>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xs mx-auto">
                                            {{ $class->kelas }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-2 mb-1">
                                            @php 
                                                $dayColor = match($class->hari) {
                                                    'Senin' => 'blue', 'Selasa' => 'emerald', 'Rabu' => 'amber',
                                                    'Kamis' => 'purple', 'Jumat' => 'rose', default => 'slate'
                                                };
                                            @endphp
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-{{ $dayColor }}-100 text-{{ $dayColor }}-700 uppercase">{{ $class->hari }}</span>
                                        </div>
                                        <p class="text-xs font-medium text-slate-500">{{ substr($class->jam_mulai, 0, 5) }} - {{ substr($class->jam_berakhir, 0, 5) }}</p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="text-sm font-semibold text-slate-600 italic tracking-tight">{{ $class->ruangan->nama_ruangan ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-5 font-black text-slate-800">{{ $class->mataKuliah->sks ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <!-- Selection Form -->
        <form action="/mahasiswa/enrollment" method="POST" id="enrollment-form" class="space-y-12">
            @csrf
            
            @php 
                $mandatory = $courses->where('sifat', 'Wajib');
                $optional = $courses->where('sifat', 'Pilihan');
            @endphp

            <!-- Mandatory Courses Section -->
            @if($mandatory->count() > 0)
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-black text-slate-800 tracking-tight">Mata Kuliah Wajib</h2>
                    <div class="h-px bg-slate-200 grow"></div>
                </div>
                <div class="grid grid-cols-1 gap-8">
                    @foreach($mandatory as $course)
                        @include('mahasiswa.enrollment.course-card', ['course' => $course, 'availableClasses' => $availableClasses])
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Optional Courses Section -->
            @if($optional->count() > 0)
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-black text-slate-800 tracking-tight">Mata Kuliah Pilihan</h2>
                    <div class="h-px bg-slate-200 grow"></div>
                </div>
                <div class="grid grid-cols-1 gap-8">
                    @foreach($optional as $course)
                        @include('mahasiswa.enrollment.course-card', ['course' => $course, 'availableClasses' => $availableClasses])
                    @endforeach
                </div>
            </div>
            @endif

            @if($courses->isEmpty())
                <div class="bg-white rounded-[2rem] p-24 border-2 border-dashed border-slate-200 text-center animate-in zoom-in duration-700">
                    <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner shadow-slate-200">
                        <svg class="w-16 h-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800 tracking-tight">Kurikulum Tidak Ditemukan</h3>
                    <p class="text-slate-500 mt-4 max-w-md mx-auto leading-relaxed">System tidak menemukan mata kuliah yang terjadwal untuk jurusan dan semester Anda.</p>
                </div>
            @endif

            <!-- Fixed Footer Actions -->
            @if(!$courses->isEmpty())
                <div class="fixed bottom-0 left-0 right-0 z-[60] p-6 lg:p-8 pointer-events-none">
                    <div class="max-w-7xl mx-auto flex justify-end pointer-events-auto">
                        <button type="submit" 
                                class="group relative inline-flex items-center gap-4 bg-slate-900 overflow-hidden text-white px-12 py-6 rounded-[2rem] font-black text-xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] hover:bg-black transition-all hover:-translate-y-2 active:translate-y-0 active:scale-95 duration-500">
                            <span class="relative z-10">Konfirmasi & Simpan DKBS</span>
                            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center relative z-10 group-hover:rotate-45 transition-transform duration-500">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 via-blue-500/20 to-emerald-500/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            
                            <!-- Shimmer effect -->
                            <div class="absolute -inset-full bg-gradient-to-r from-transparent via-white/10 to-transparent skew-x-[-20deg] animate-shimmer pointer-events-none"></div>
                        </button>
                    </div>
                </div>
            @endif
        </form>
    @endif
</div>

@if(!$isEnrolled)
<script>
    function unselectCourse(kodeMk) {
        const radios = document.getElementsByName(`selections[${kodeMk}]`);
        radios.forEach(r => r.checked = false);
        calculateTotalSks();
    }

    function calculateTotalSks() {
        let total = 0;
        const selected = document.querySelectorAll('input[type="radio"]:checked');
        selected.forEach(r => {
            total += parseInt(r.getAttribute('data-sks') || 0);
        });
        document.getElementById('total-sks-display').innerText = total;
    }

    // Attach listeners
    document.querySelectorAll('input[type="radio"]').forEach(r => {
        r.addEventListener('change', calculateTotalSks);
    });

    // Initial calculation
    document.addEventListener('DOMContentLoaded', calculateTotalSks);
</script>
@endif

<style>
    @keyframes shimmer {
        from { transform: translateX(-100%); }
        to { transform: translateX(100%); }
    }
    .animate-shimmer {
        animation: shimmer 3s infinite;
    }
    .radio-card-checked {
        border-color: #4f46e5;
        background: #f8faff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }
</style>
@endsection
