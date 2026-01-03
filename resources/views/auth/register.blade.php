<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar | EduTrack</title>
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

        .custom-input {
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-input:focus {
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%);
            transition: all 0.4s ease;
        }

        .btn-gradient:hover {
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
            transform: translateY(-2px);
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
    <div class="absolute inset-0 bg-gradient-to-tr from-indigo-900/40 to-transparent z-0"></div>

    <!-- WRAPPER -->
    <div class="w-full max-w-lg z-10 animate-fade-in">
        
        <!-- CARD -->
        <div class="glass-card rounded-[2.5rem] shadow-2xl overflow-hidden p-8 md:p-12 relative">
            
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Buat Akun Baru</h1>
                <p class="text-slate-500 font-medium">Bergabung dengan ekosistem EduTrack</p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="text-xs font-bold uppercase tracking-wider">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ url('/register') }}" id="regForm" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Masukkan nama Anda"
                        class="custom-input w-full border border-slate-200 rounded-2xl py-3.5 px-6 text-slate-800 font-semibold outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@contoh.com"
                        class="custom-input w-full border border-slate-200 rounded-2xl py-3.5 px-6 text-slate-800 font-semibold outline-none focus:border-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">ID (NRP/NIP)</label>
                        <input type="text" name="nrp" value="{{ old('nrp') }}" required placeholder="ID"
                            class="custom-input w-full border border-slate-200 rounded-2xl py-3.5 px-6 text-slate-800 font-semibold outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Role</label>
                        <select name="role" required class="custom-input w-full border border-slate-200 rounded-2xl py-3.5 px-4 text-slate-800 font-semibold outline-none focus:border-blue-500 appearance-none bg-no-repeat bg-[right_1rem_center]">
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="dosen">Dosen</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Kata Sandi</label>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="custom-input w-full border border-slate-200 rounded-2xl py-3.5 px-6 text-slate-800 font-semibold outline-none focus:border-blue-500">
                </div>

                <button id="regBtn" class="btn-gradient w-full py-4 rounded-[1.25rem] text-white font-bold text-lg shadow-xl shadow-blue-200 flex items-center justify-center gap-3 active:scale-95 transition-all mt-6">
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-slate-400 font-medium tracking-wide">
                    Sudah punya akun? 
                    <a href="{{ url('/login') }}" class="text-blue-600 font-bold hover:underline underline-offset-4 decoration-2">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('regForm');
        const btn = document.getElementById('regBtn');

        form.addEventListener('submit', () => {
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;
        });
    </script>
</body>
</html>