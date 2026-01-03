<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title','Portal Mahasiswa')</title>
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
            background-color: rgb(239 246 255);
            color: rgb(37 99 235);
        }
        .nav-item-inactive {
            color: rgb(100 116 139);
        }
        .nav-item-inactive:hover {
            background-color: rgb(248 250 252);
            color: rgb(37 99 235);
        }
        /* Hide scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-slate-50 text-slate-600 antialiased selection:bg-blue-100 selection:text-blue-700">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="hidden md:flex flex-col w-72 bg-white border-r border-slate-200 h-full flex-shrink-0 z-20">
        <!-- BRAND -->
        <div class="h-20 flex items-center px-8 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 002.21 10.04L2.16 10a.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z" />
                        <path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.255 4.285a.75.75 0 01-.46.71 47.878 47.878 0 00-8.105 4.342.75.75 0 01-.832 0 47.877 47.877 0 00-8.104-4.342.75.75 0 01-.461-.71c.035-1.442.121-2.87.255-4.286A48.4 48.4 0 0110.94 15.473c.35.186.77.186 1.12 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-xl tracking-tight text-slate-800">EduTrack</h1>
                    <p class="text-[10px] text-blue-600 font-bold uppercase tracking-widest">Student Portal</p>
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
                <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-blue-500/20 rounded-full blur-xl group-hover:bg-blue-500/30 transition"></div>
            </div>
        </div>

        <!-- NAVIGATION -->
        <nav class="flex-1 px-4 space-y-1 overflow-y-auto no-scrollbar pb-6">
            <p class="px-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-2">Academic</p>
            
            <a href="/mahasiswa/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('mahasiswa/dashboard') ? 'nav-item-active shadow-sm' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                Dashboard
            </a>

            <a href="/mahasiswa/jadwal" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('mahasiswa/jadwal*') ? 'nav-item-active shadow-sm' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                Jadwal Kuliah
            </a>

            <a href="/mahasiswa/presensi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('mahasiswa/presensi*') ? 'nav-item-active shadow-sm' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Presensi
            </a>

            <a href="/mahasiswa/dkbs" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('mahasiswa/dkbs*') ? 'nav-item-active shadow-sm' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Rencana Studi (DKBS)
            </a>

            <a href="/mahasiswa/nilai" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('mahasiswa/nilai*') ? 'nav-item-active shadow-sm' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                Transkrip Nilai
            </a>
            
            <p class="px-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-6">General</p>

            <a href="/mahasiswa/pembayaran" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('mahasiswa/pembayaran*') ? 'nav-item-active shadow-sm' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                Keuangan & Tagihan
            </a>

            <a href="/mahasiswa/pengumuman" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('mahasiswa/pengumuman*') ? 'nav-item-active shadow-sm' : 'nav-item-inactive' }}">
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
                <!-- Search (Functional with Dropdown) -->
                <div class="relative hidden md:block" id="search-container">
                    <div class="flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-full text-slate-400 text-sm focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input type="text" id="global-search" placeholder="Pencarian Cepat..." class="bg-transparent border-none outline-none text-slate-600 placeholder:text-slate-400 w-48 focus:w-64 transition-all" autocomplete="off">
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div id="search-results-dropdown" class="absolute top-full right-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 hidden z-50 overflow-hidden animate-fade-in-up">
                        <div class="p-3 border-b border-slate-50 bg-slate-50/50">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Hasil Pencarian Menu</span>
                        </div>
                        <div id="dropdown-items-container" class="max-h-64 overflow-y-auto no-scrollbar">
                            <!-- Dynamic items -->
                        </div>
                    </div>
                </div>

                <div class="w-px h-8 bg-slate-200 mx-2"></div>

                <a href="/mahasiswa/profile" class="flex items-center gap-2 hover:bg-slate-100 rounded-lg p-2 transition">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold ring-2 ring-white shadow-sm">
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

<script>
    const globalSearch = document.getElementById('global-search');
    const dropdown = document.getElementById('search-results-dropdown');
    const dropdownItems = document.getElementById('dropdown-items-container');

    globalSearch?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        
        // 1. Filter Table Rows (Existing Functionality)
        const rows = document.querySelectorAll('tbody tr:not(.no-results-msg)');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });

        // 2. Populate Dropdown with Menu Matches
        if (query.length > 0) {
            const menuLinks = document.querySelectorAll('aside nav a');
            let resultsHTML = '';
            let count = 0;

            menuLinks.forEach(link => {
                const text = link.innerText.trim();
                if (text.toLowerCase().includes(query)) {
                    const icon = link.querySelector('svg').outerHTML;
                    resultsHTML += `
                        <a href="${link.getAttribute('href')}" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition-colors group border-b border-slate-50 last:border-0">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                                ${icon.replace('w-5 h-5', 'w-4 h-4')}
                            </div>
                            <span class="text-sm font-medium text-slate-700 group-hover:text-blue-700">${text}</span>
                        </a>
                    `;
                    count++;
                }
            });

            if (count > 0) {
                dropdownItems.innerHTML = resultsHTML;
                dropdown.classList.remove('hidden');
            } else {
                dropdownItems.innerHTML = `
                    <div class="p-8 text-center text-slate-400">
                        <p class="text-xs italic">Menu tidak ditemukan</p>
                    </div>
                `;
                dropdown.classList.remove('hidden');
            }
        } else {
            dropdown.classList.add('hidden');
        }

        // Handle Table Empty State
        document.querySelectorAll('tbody').forEach(tbody => {
            let noResults = tbody.querySelector('#search-no-results');
            const visibleRows = tbody.querySelectorAll('tr:not(.no-results-msg):not([style*="display: none"])');
            if (visibleRows.length === 0 && query !== '') {
                if (!noResults) {
                    noResults = document.createElement('tr');
                    noResults.id = 'search-no-results';
                    noResults.className = 'no-results-msg';
                    noResults.innerHTML = `<td colspan="100%" class="py-12 text-center text-slate-400 italic">Data tidak ditemukan</td>`;
                    tbody.appendChild(noResults);
                }
            } else if (noResults) {
                noResults.remove();
            }
        });
    });

    // Close dropdown on click outside
    document.addEventListener('click', (e) => {
        if (!document.getElementById('search-container')?.contains(e.target)) {
            dropdown?.classList.add('hidden');
        }
    });
</script>

</body>
</html>