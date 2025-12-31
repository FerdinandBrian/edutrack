<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title','Dashboard Dosen')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-gradient-to-b from-green-600 to-green-400 shadow-lg flex flex-col text-white">
        <!-- LOGO -->
        <div class="px-6 py-5 flex items-center gap-3 border-b border-white/20">
            <div class="w-10 h-10 bg-white/20 text-white rounded-lg flex items-center justify-center font-bold">
                ET
            </div>
            <div>
                <p class="font-semibold text-white">EduTrack</p>
                <p class="text-xs text-white/80">Dosen</p>
            </div>
        </div>

        <!-- MENU -->
        <nav class="flex-1 px-4 py-6 space-y-1 text-sm">
            <a href="/dosen/dashboard" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ Request::is('dosen/dashboard') ? 'bg-white/20 text-white font-medium' : 'hover:bg-white/10 text-white/80' }}">
                📊 Dashboard
            </a>
            <a href="/dosen/presensi" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ Request::is('dosen/presensi*') ? 'bg-white/20 text-white font-medium' : 'hover:bg-white/10 text-white/80' }}">
                ⏱ Presensi
            </a>
            <a href="/dosen/nilai" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ Request::is('dosen/nilai*') ? 'bg-white/20 text-white font-medium' : 'hover:bg-white/10 text-white/80' }}">
                📈 Nilai
            </a>
            <a href="/dosen/jadwal" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ Request::is('dosen/jadwal*') ? 'bg-white/20 text-white font-medium' : 'hover:bg-white/10 text-white/80' }}">
                📅 Jadwal
            </a>
            <a href="/dosen/profile" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ Request::is('dosen/profile*') ? 'bg-white/20 text-white font-medium' : 'hover:bg-white/10 text-white/80' }}">
                👤 Profil Saya
            </a>
        </nav>

        <!-- LOGOUT -->
        <div class="px-4 py-4 border-t border-white/20">
            <form method="POST" action="/logout">
                @csrf
                <button class="w-full text-left px-4 py-2 rounded-lg text-red-200 hover:bg-white/20 text-sm">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col">

        <!-- TOPBAR -->
        <header class="bg-white shadow px-8 py-4 flex items-center justify-between">
            <h1 class="font-semibold text-slate-800">
                Dashboard Dosen
            </h1>
            <div class="flex items-center gap-3">
                <div class="text-right text-sm">
                    <p class="font-medium">{{ auth()->user()->nrp }}</p>
                    <p class="text-slate-500">Dosen</p>
                </div>
                <a href="/dosen/profile" class="w-9 h-9 rounded-full bg-green-600 text-white flex items-center justify-center font-semibold hover:bg-green-700 transition-colors cursor-pointer">
                    {{ substr(auth()->user()->nama,0,1) }}
                </a>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="p-8">
            <!-- WELCOME BANNER -->
            <div class="mb-8 bg-gradient-to-r from-green-600 to-green-400 text-white rounded-2xl p-6 shadow">
                <h2 class="text-lg font-semibold">
                    Selamat datang, {{ auth()->user()->nama }}!
                </h2>
                <p class="text-sm text-white/90 mt-1">
                    Semoga aktivitas akademikmu hari ini berjalan lancar.
                </p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>

    </div>
</div>

</body>
</html>