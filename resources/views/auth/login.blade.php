<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | EduTrack</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
        }
        
        .bg-login {
            background-image: url('/assets/login-bg.png');
            background-size: cover;
            background-position: center;
            filter: brightness(0.7) blur(2px);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .input-group:focus-within label {
            color: #2563eb;
            transform: translateY(-2px);
        }

        .custom-input {
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-input:focus {
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%);
            transition: all 0.4s ease;
        }

        .btn-gradient:hover {
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
    </style>
</head>

<body class="min-h-screen relative flex items-center justify-center p-6 overflow-hidden">
    
    <!-- Background Layer -->
    <div class="absolute inset-0 bg-login z-0"></div>
    <div class="absolute inset-0 bg-gradient-to-tr from-blue-900/40 to-transparent z-0"></div>

    <!-- Decorative Elements -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-500/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-cyan-500/20 rounded-full blur-[120px] pointer-events-none"></div>

    <!-- WRAPPER -->
    <div class="w-full max-w-lg z-10 animate-fade-in">
        
        <!-- CARD -->
        <div class="glass-card rounded-[2.5rem] shadow-2xl overflow-hidden p-8 md:p-12 relative">
            
            <!-- Header section -->
            <div class="text-center mb-10">
                <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-blue-600 to-cyan-400 rounded-3xl flex items-center justify-center shadow-2xl shadow-blue-500/30 float-animation">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">EduTrack</h1>
                <p class="text-slate-500 font-medium">Platform Akademik Terintegrasi</p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-8 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl flex items-center gap-3 animate-pulse">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="text-xs font-bold leading-tight uppercase tracking-wider">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}" id="loginForm" class="space-y-6">
                @csrf

                <div class="input-group">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1 transition-all">
                        Nomor Identitas
                    </label>
                    <div class="relative group">
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <input type="text" name="nrp" value="{{ old('nrp') }}" required autofocus placeholder="NRP / NIP / NIK"
                            class="custom-input w-full border border-slate-200 rounded-2xl py-4 pl-14 pr-6 text-slate-800 font-semibold placeholder:text-slate-300 placeholder:font-normal outline-none">
                    </div>
                </div>

                <div class="input-group">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1 transition-all">
                        Kata Sandi
                    </label>
                    <div class="relative group">
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <input type="password" name="password" required placeholder="••••••••"
                            class="custom-input w-full border border-slate-200 rounded-2xl py-4 pl-14 pr-6 text-slate-800 font-semibold placeholder:text-slate-300 placeholder:font-normal outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="remember" class="peer h-5 w-5 cursor-pointer appearance-none rounded-lg border-2 border-slate-200 bg-white transition-all checked:border-blue-500 checked:bg-blue-500">
                            <svg class="absolute left-1/2 top-1/2 h-4 w-4 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 transition-opacity peer-checked:opacity-100" fill="none" stroke="currentColor" stroke-width="4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-500 group-hover:text-slate-700 transition">Ingat saya</span>
                    </label>
                    <a href="#" class="text-sm font-bold text-blue-600 hover:text-blue-700 transition">Lupa Sandi?</a>
                </div>

                <button id="loginBtn" class="btn-gradient w-full py-4 rounded-[1.25rem] text-white font-bold text-lg shadow-xl shadow-blue-200 flex items-center justify-center gap-3 active:scale-95 transition-all">
                    Masuk ke Portal
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </button>
            </form>

            <div class="mt-12 text-center">
                <p class="text-sm text-slate-400 font-medium tracking-wide">
                    Belum memiliki akun? 
                    <a href="{{ url('/register') }}" class="text-blue-600 font-bold hover:underline underline-offset-4 decoration-2">Daftar Akun</a>
                </p>
            </div>
        </div>

        <!-- System Status -->
        <div class="mt-8 flex items-center justify-center gap-6">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                <span class="text-[10px] font-bold text-white/50 uppercase tracking-widest">System Online</span>
            </div>
            <div class="h-4 w-px bg-white/10"></div>
            <span class="text-[10px] items-center flex gap-1 font-bold text-white/50 uppercase tracking-widest">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                Secure SSL 256-bit
            </span>
        </div>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('loginBtn');

        form.addEventListener('submit', () => {
            btn.disabled = true;
            btn.style.opacity = '0.8';
            btn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menghubungkan...
            `;
        });
    </script>
</body>
</html>