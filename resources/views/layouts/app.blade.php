<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sistem Akademik')</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2>SIAKAD</h2>
        <ul>
            @auth
                @if(auth()->user()->idRole == 1)
                    <li><a href="/admin/dashboard">Dashboard</a></li>
                @endif

                @if(auth()->user()->idRole == 2)
                    <li><a href="/presensi">Presensi</a></li>
                    <li><a href="/nilai">Nilai</a></li>
                @endif

                @if(auth()->user()->idRole == 3)
                    <li><a href="/jadwal">Jadwal</a></li>
                    <li><a href="/tagihan">Tagihan</a></li>
                @endif

                <li>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                </li>
            @endauth
        </ul>
    </aside>

    <!-- CONTENT -->
    <main class="content">
        @yield('content')
    </main>

</body>
</html>
