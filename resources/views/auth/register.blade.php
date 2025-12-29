<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register | Sistem Akademik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        input:focus, select:focus {
            border-color: transparent !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,.1), 0 0 0 2px #2563eb !important;
        }
        .register-btn:hover:not(:disabled) {
            box-shadow: 0 15px 35px rgba(37,99,235,.4);
            transform: translateY(-2px);
        }
        .register-btn:active:not(:disabled) { transform: translateY(0); }
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

<div class="w-full max-w-lg">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-br from-blue-600 to-cyan-500 px-10 py-8 text-center text-white">
            <h1 class="text-2xl font-bold">Registrasi Akun</h1>
            <p class="text-sm text-white/90">Silakan daftar untuk melanjutkan</p>
        </div>

        <!-- Body -->
        <div class="p-10">
            <!-- Error -->
            @if ($errors->any())
                <div class="alert mb-6 flex gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-5 h-5 stroke-red-700 fill-none stroke-2 mt-0.5" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ url('/register') }}" class="space-y-5">
                @csrf

                <!-- Role (Fixed to Mahasiswa for Public Register) -->
                <input type="hidden" name="role" value="mahasiswa">

                <!-- Nama -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <input name="nama" value="{{ old('nama') }}" required
                           class="w-full border border-slate-300 rounded-xl px-4 py-3">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-slate-300 rounded-xl px-4 py-3">
                </div>

                <!-- Nomor Telepon -->
                <label class="block text-sm font-medium text-slate-700 mb-1">No Telepon</label>
                    <input name="no_telepon" placeholder="No Telepon" value="{{ old('no_telepon') }}" class="w-full border border-slate-300 rounded-xl px-4 py-3">

                <!-- Tanggal Lahir -->
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" placeholder="Tanggal Lahir" value="{{ old('tanggal_lahir') }}" class="w-full border border-slate-300 rounded-xl px-4 py-3">

                <!-- Mahasiswa Fields (Auto Show) -->
                <div id="mahasiswa-fields" class="space-y-3">
                    <label class="block text-sm font-medium text-slate-700 mb-1">NRP</label>
                    <input name="nrp" placeholder="NRP" value="{{ old('nrp') }}" required class="w-full border border-slate-300 rounded-xl px-4 py-3">

                    <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                    <input name="alamat" placeholder="Alamat" value="{{ old('alamat') }}" required class="w-full border border-slate-300 rounded-xl px-4 py-3">

                    <label class="block text-sm font-medium text-slate-700 mb-1">Jurusan</label>
                    <input name="jurusan" placeholder="Jurusan" value="{{ old('jurusan') }}" required class="w-full border border-slate-300 rounded-xl px-4 py-3">
                </div>

                <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kelamin</label>
                    <input type="text" name="jenis_kelamin" placeholder="Laki-laki / Perempuan"
                        value="{{ old('jenis_kelamin') }}" required
                        class="w-full border border-slate-300 rounded-xl px-4 py-3">

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full border border-slate-300 rounded-xl px-4 py-3">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required class="w-full border border-slate-300 rounded-xl px-4 py-3">
                </div>

                <!-- Button -->
                <button id="registerBtn" type="submit"
                        class="register-btn w-full bg-gradient-to-br from-blue-600 to-cyan-500
                                text-white font-semibold py-3 rounded-xl shadow-lg
                                flex items-center justify-center gap-2 transition text-base">
                    <span>Daftar</span>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t text-center text-sm text-slate-600">
                Sudah punya akun?
                <a href="{{ url('/login') }}" class="text-blue-600 font-semibold hover:underline">
                    Login
                </a>
            </div>
        </div>
    </div>

    <p class="text-center text-sm text-slate-500 mt-6">
        © 2025 Sistem Akademik
    </p>
</div>

<script>
// Spinner on submit
const form = document.querySelector('form');
const btn = document.getElementById('registerBtn');
form.addEventListener('submit', () => {
    btn.disabled = true;
    btn.querySelector('span').textContent = 'Memproses...';
});
</script>

</body>
</html>