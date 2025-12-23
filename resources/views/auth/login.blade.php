<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Sistem Akademik</title>

    <!-- CSS dari template Anda -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="login-page">

    <div class="login-container">
        <h2>Login Sistem Akademik</h2>

        {{-- ERROR LOGIN --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="form-group">
                <label for="npr">NPR / NIDN</label>
                <input 
                    type="text" 
                    id="npr" 
                    name="npr" 
                    value="{{ old('npr') }}" 
                    required 
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                >
            </div>

            <div class="form-group">
                <button type="submit" class="btn-login">
                    Login
                </button>
            </div>
        </form>
    </div>

</body>
</html>
