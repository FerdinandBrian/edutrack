<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Sistem Akademik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        input:focus {
            border-color: transparent !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,.1), 0 0 0 2px #2563eb !important;
        }
        .login-btn:hover:not(:disabled) {
            box-shadow: 0 15px 35px rgba(37,99,235,.4);
            transform: translateY(-2px);
        }
        .login-btn:active:not(:disabled) {
            transform: translateY(0);
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes shake {
            0%,100%{transform:translateX(0)}
            25%{transform:translateX(-8px)}
            75%{transform:translateX(8px)}
        }
        .spinner { animation: spin .8s linear infinite }
        .alert { animation: shake .3s ease-in-out }
    </style>
</head>

<body class="min-h-screen bg-white flex items-center justify-center px-6">

<!-- WRAPPER -->
<div class="w-full max-w-lg">

    <!-- CARD -->
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">

        <!-- HEADER -->
        <div class="bg-gradient-to-br from-blue-600 to-cyan-500 px-10 py-8 text-center text-white">
            <div class="w-14 h-14 mx-auto mb-4 flex items-center justify-center rounded-full bg-white/20 backdrop-blur">
                <svg class="w-8 h-8 stroke-white fill-none stroke-2" viewBox="0 0 24 24">
                    <path d="M4 19V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/>
                    <path d="M9 9h6M9 13h6"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold">Sistem Akademik</h1>
            <p class="text-sm text-white/90">Silakan login untuk melanjutkan</p>
        </div>

        <!-- BODY -->
        <div class="p-10">

            <!-- ERROR -->
            @if ($errors->any())
                <div class="alert mb-6 flex gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-5 h-5 stroke-red-700 fill-none stroke-2 mt-0.5" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}" id="loginForm" class="space-y-6">
                @csrf

                <!-- NRP -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        NRP / NIK
                    </label>
                    <div class="relative">
                        <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 stroke-2 fill-none" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <path d="M12 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/>
                        </svg>
                        <input type="text" name="nrp" value="{{ old('nrp') }}" required autofocus
                            class="w-full border border-slate-300 rounded-xl pl-3 pr-4 py-3 text-base">
                    </div>
                </div>

                <!-- PASSWORD -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 stroke-2 fill-none" viewBox="0 0 24 24">
                            <path d="M3 11h18"/>
                            <path d="M5 11v-2a5 5 0 0 1 10 0v2"/>
                            <path d="M7 11v8a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-8"/>
                        </svg>
                        <input type="password" name="password" required
                            class="w-full border border-slate-300 rounded-xl pl-3 pr-4 py-3 text-base">
                    </div>
                </div>

                <!-- OPTIONS -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember" class="accent-blue-600 w-4 h-4">
                        Ingat saya
                    </label>
                    <a href="#" class="text-blue-600 font-medium hover:underline">
                        Lupa password?
                    </a>
                </div>

                <!-- BUTTON -->
                <button id="loginBtn"
                    class="login-btn w-full bg-gradient-to-br from-blue-600 to-cyan-500
                           text-white font-semibold py-3 rounded-xl shadow-lg
                           flex items-center justify-center gap-2 transition text-base">
                    Login
                </button>
            </form>

            <!-- REGISTER -->
            <div class="mt-8 pt-6 border-t text-center text-sm text-slate-600">
                Belum punya akun?
                <a href="{{ url('/register') }}" 
                    class="text-blue-600 font-semibold hover:underline">
                    Daftar disini
                </a>
            </div>

        </div>
    </div>

    <!-- FOOTER -->
    <p class="text-center text-sm text-slate-500 mt-6">
        © 2025 Sistem Akademik
    </p>
</div>

<script>
const form = document.getElementById('loginForm');
const btn = document.getElementById('loginBtn');

form.addEventListener('submit', () => {
    btn.disabled = true;
    btn.innerHTML = `
        <div class="spinner w-5 h-5 border-2 border-white/30 border-t-white rounded-full"></div>
        Memproses...
    `;
});
</script>

</body>
</html>