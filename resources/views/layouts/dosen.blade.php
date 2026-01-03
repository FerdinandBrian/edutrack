<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title','Portal Dosen')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
        }
        .nav-item-active {
            background-color: rgb(236 253 245); /* emerald-50 */
            color: rgb(5 150 105); /* emerald-600 */
        }
        .nav-item-inactive {
            color: rgb(100 116 139);
        }
        .nav-item-inactive:hover {
            background-color: rgb(248 250 252);
            color: rgb(5 150 105);
        }
        /* Hide scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-slate-50 text-slate-600 antialiased selection:bg-emerald-100 selection:text-emerald-700">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="hidden md:flex flex-col w-72 bg-white border-r border-slate-200 h-full flex-shrink-0 z-20">
        <!-- BRAND -->
        <div class="h-20 flex items-center px-8 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-tr from-emerald-600 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L1 7l11 5 9-4.09V17h2V7L12 2zm1 14.36V22h-2v-5.64l-8-3.64v3.18l9 4.09 9-4.09v-3.18l-8 3.64z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-xl tracking-tight text-slate-800">EduTrack</h1>
                    <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-widest">Lecturer Portal</p>
                </div>
            </div>
        </div>

        <!-- USER CARD SMALL -->
        <div class="px-6 py-6">
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-4 text-white shadow-xl shadow-slate-200/50 relative overflow-hidden group">
                <div class="relative z-10 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-700 border-2 border-slate-600 flex items-center justify-center font-bold text-sm">
                        {{ substr(auth()->user()->nama,0,1) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold truncate w-32">{{ auth()->user()->nama }}</p>
                        <p class="text-[10px] text-slate-400 font-mono">{{ auth()->user()->nrp }}</p>
                    </div>
                </div>
                <!-- Decorative Circle -->
                <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-emerald-500/20 rounded-full blur-xl group-hover:bg-emerald-500/30 transition"></div>
            </div>
        </div>

        <!-- NAVIGATION -->
        <nav class="flex-1 px-4 space-y-1 overflow-y-auto no-scrollbar pb-6">
            <p class="px-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-2">Academic</p>
            
            <a href="/dosen/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('dosen/dashboard') ? 'nav-item-active shadow-sm' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                Dashboard
            </a>

            <a href="/dosen/jadwal" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('dosen/jadwal*') ? 'nav-item-active shadow-sm' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                Jadwal Mengajar
            </a>

            <a href="/dosen/presensi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('dosen/presensi*') ? 'nav-item-active shadow-sm' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Presensi Mahasiswa
            </a>

            <a href="/dosen/nilai" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('dosen/nilai*') ? 'nav-item-active shadow-sm' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                Input Nilai
            </a>
            
            <p class="px-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-6">General</p>

            <a href="/dosen/pengumuman" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('dosen/pengumuman*') ? 'nav-item-active shadow-sm' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                Pengumuman
            </a>

            <div class="mt-8 px-4">
                 <form method="POST" action="/logout">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-red-100 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition font-semibold text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-50/50">
        
        <!-- HEADER MOBILE & DESKTOP -->
        <header class="h-20 glass-header sticky top-0 z-30 px-6 md:px-10 flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">@yield('title')</h2>
            
            <div class="flex items-center gap-4">
                 <!-- Search (Visual Only) -->
                <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-full text-slate-400 text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <span>Pencarian Cepat...</span>
                </div>

                <div class="w-px h-8 bg-slate-200 mx-2"></div>

                <a href="/dosen/profile" class="flex items-center gap-2 hover:bg-slate-100 rounded-lg p-2 transition">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold ring-2 ring-white shadow-sm">
                         {{ substr(auth()->user()->nama,0,1) }}
                    </div>
                    <span class="text-sm font-medium text-slate-600 hidden md:inline-block">Account</span>
                </a>
            </div>
        </header>

        <!-- CONTENT SCROLL -->
        <main class="flex-1 overflow-y-auto p-6 md:p-10 scroll-smooth">
            <div class="max-w-6xl mx-auto">
                 @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl flex items-center gap-3 animate-fade-in-down">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl flex items-center gap-3 animate-fade-in-down">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>

    </div>
</div>

</body>
</html>