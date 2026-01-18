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
                 <form id="logout-form" method="POST" action="/logout" class="hidden">
                    @csrf
                </form>
                <button type="button" onclick="showLogoutModal()" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-red-100 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition font-semibold text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Sign Out
                </button>
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
                    <div class="flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-full text-slate-400 text-sm focus-within:ring-2 focus-within:ring-emerald-100 transition-all">
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

<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-sm transform overflow-hidden rounded-3xl bg-white p-8 shadow-2xl transition-all animate-fade-in-up">
            <div class="text-center">
                <!-- Icon -->
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50 text-red-500">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-slate-900">Konfirmasi Keluar</h3>
                <p class="mt-2 text-sm text-slate-500">Anda akan meninggalkan sesi Anda. Yakin ingin keluar?</p>
                
                <div class="mt-8 flex flex-col gap-3">
                    <button type="button" onclick="confirmLogout()" class="w-full rounded-xl bg-red-500 py-3 text-sm font-bold text-white shadow-lg shadow-red-200 transition-all hover:bg-red-600 active:scale-95">
                        Ya, Keluar
                    </button>
                    <button type="button" onclick="hideLogoutModal()" class="w-full rounded-xl bg-slate-100 py-3 text-sm font-bold text-slate-600 transition-all hover:bg-slate-200 active:scale-95">
                        Batalkan
                    </button>
                </div>
            </div>
        </div>
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
                        <a href="${link.getAttribute('href')}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 transition-colors group border-b border-slate-50 last:border-0">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all">
                                ${icon.replace('w-5 h-5', 'w-4 h-4')}
                            </div>
                            <span class="text-sm font-medium text-slate-700 group-hover:text-emerald-700">${text}</span>
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

    // Logout Modal Functions
    function showLogoutModal() {
        const modal = document.getElementById('logout-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideLogoutModal() {
        const modal = document.getElementById('logout-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function confirmLogout() {
        document.getElementById('logout-form').submit();
    }
</script>

</body>
</html>