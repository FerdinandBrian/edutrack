<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title','Dashboard Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-600 antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-72 bg-slate-900 text-white flex flex-col border-r border-slate-800 flex-shrink-0 transition-all duration-300">
        <!-- LOGO -->
        <div class="h-20 flex items-center px-8 border-b border-slate-800/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-lg tracking-tight">EduTrack</h1>
                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Admin Portal</p>
                </div>
            </div>
        </div>

        <!-- MENU -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto no-scrollbar">
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Main Menu</p>
            
            <a href="/admin/dashboard" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ Request::is('admin/dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-blue-400' }}">
                <svg class="w-5 h-5 {{ Request::is('admin/dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="font-medium text-sm">Dashboard</span>
            </a>

            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-6 mb-2">Academic</p>

            <a href="/admin/mata-kuliah" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ Request::is('admin/mata-kuliah*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-blue-400' }}">
                <svg class="w-5 h-5 {{ Request::is('admin/mata-kuliah*') ? 'text-white' : 'text-slate-500 group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="font-medium text-sm">Mata Kuliah</span>
            </a>

            <a href="/admin/perkuliahan" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ Request::is('admin/perkuliahan*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-blue-400' }}">
                <svg class="w-5 h-5 {{ Request::is('admin/perkuliahan*') ? 'text-white' : 'text-slate-500 group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="font-medium text-sm">Jadwal Kelas</span>
            </a>

            <a href="/admin/dkbs" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ Request::is('admin/dkbs*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-blue-400' }}">
                <svg class="w-5 h-5 {{ Request::is('admin/dkbs*') ? 'text-white' : 'text-slate-500 group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="font-medium text-sm">Kelola DKBS</span>
            </a>

            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-6 mb-2">Management</p>

            <a href="/admin/users" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ Request::is('admin/users*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-blue-400' }}">
                <svg class="w-5 h-5 {{ Request::is('admin/users*') ? 'text-white' : 'text-slate-500 group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span class="font-medium text-sm">Users</span>
            </a>

            <a href="/admin/pembayaran" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ Request::is('admin/pembayaran*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-blue-400' }}">
                <svg class="w-5 h-5 {{ Request::is('admin/pembayaran*') ? 'text-white' : 'text-slate-500 group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                <span class="font-medium text-sm">Pembayaran</span>
            </a>
            
            <a href="/admin/pengumuman" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ Request::is('admin/pengumuman*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-blue-400' }}">
                <svg class="w-5 h-5 {{ Request::is('admin/pengumuman*') ? 'text-white' : 'text-slate-500 group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                <span class="font-medium text-sm">Pengumuman</span>
            </a>
        </nav>

        <!-- USER PROFILE -->
        <div class="px-4 py-4 border-t border-slate-800 bg-slate-900">
            <div class="flex items-center gap-3 px-2 mb-4">
                <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-xs font-bold text-white ring-2 ring-slate-600">
                    {{ substr(auth()->user()->nama,0,1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->nama }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-2">
                <a href="/admin/profile" class="flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 hover:text-white transition group">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Profile
                </a>
                <form method="POST" action="/logout">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-xs text-red-400 hover:text-red-300 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- HEADER -->
        <header class="h-20 glass-effect border-b border-slate-200/60 sticky top-0 z-40 px-8 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">@yield('title')</h2>
                
                <!-- Search (Functional with Dropdown) -->
                <div class="relative hidden lg:block" id="search-container">
                    <div class="flex items-center gap-2 px-4 py-2 bg-slate-100/50 rounded-full text-slate-400 text-sm focus-within:ring-2 focus-within:ring-blue-100 focus-within:bg-white transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input type="text" id="global-search" placeholder="Pencarian Cepat..." class="bg-transparent border-none outline-none text-slate-600 placeholder:text-slate-400 w-48 focus:w-64 transition-all" autocomplete="off">
                    </div>

                    <!-- Search Results Dropdown -->
                    <div id="search-results-dropdown" class="absolute top-full left-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 hidden z-50 overflow-hidden">
                        <div class="p-3 border-b border-slate-50 bg-slate-50/50">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Hasil Pencarian Menu</span>
                        </div>
                        <div id="dropdown-items-container" class="max-h-64 overflow-y-auto no-scrollbar">
                            <!-- Dynamic items -->
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-6">
                <!-- Rest of header items ... -->
                <a href="/admin/notifications" class="relative p-2 text-slate-400 hover:text-slate-600 transition">
                    @php
                        // Check for any Lunas notification that has a higher ID than what we last saw
                        // This avoids all datetime comparison bugs
                        $lastReadId = session('read_notification_id', 0);
                        
                        $hasNewNotifications = \App\Models\Tagihan::where('status', 'Lunas')
                            ->where('id', '>', $lastReadId)
                            ->exists();
                    @endphp
                    @if($hasNewNotifications)
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
                    @endif
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                </a>
                
                <div class="h-8 w-px bg-slate-200"></div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-bold text-slate-700">{{ auth()->user()->nama }}</p>
                        <p class="text-xs text-slate-500">Administrator</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN SCROLLABLE CONTENT -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth">
            <div class="max-w-7xl mx-auto">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-xl flex items-center gap-3 animate-fade-in-down shadow-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-xl flex items-center gap-3 animate-fade-in-down shadow-sm">
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
        
        // 1. Filter Table Rows
        const rows = document.querySelectorAll('tbody tr:not(.no-results-msg)');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });

        // 2. Populate Dropdown
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
                dropdownItems.innerHTML = `<div class="p-8 text-center text-slate-400 italic text-xs">Menu tidak ditemukan</div>`;
                dropdown.classList.remove('hidden');
            }
        } else {
            dropdown.classList.add('hidden');
        }

        // Table empty state
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

    document.addEventListener('click', (e) => {
        if (!document.getElementById('search-container')?.contains(e.target)) {
            dropdown?.classList.add('hidden');
        }
    });
</script>

</body>
</html>