<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Ssyar'S-Intel</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <!-- Login Page -->
    <div id="loginPage" class="login-container">
        <div class="login-card">
            <div class="login-left">
                <p class="bismillah">Bismillahirrahmanirrahim</p>
                <p class="assalamu">السلام عليكم</p>
                <img src="https://i.postimg.cc/WzGLKfbL/logo-ssyar-s-intel.png" alt="Logo" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 100 100%27%3E%3Ccircle cx=%2750%27 cy=%2750%27 r=%2745%27 fill=%27%232d5016%27/%3E%3Ctext x=%2750%27 y=%2765%27 text-anchor=%27middle%27 fill=%27%23ffd700%27 font-size=%2740%27 font-weight=%27bold%27%3ESI%3C/text%3E%3C/svg%3E'">
                <h1>Ssyar's-Intel</h1>
                <p>Smart Syariah Surveillance Innovation Technology</p>
            </div>
            <div class="login-right">
                <!-- Sign In Form -->
                <form id="signInForm" method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <h2>Masuk ke Sistem</h2>
                    <p class="login-subtitle">Silakan masuk dengan akun Anda</p>
                    
                    @if(session('success'))
                        <div class="success-message" style="display: block;">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="error-message" style="display: block; background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="signInEmail" placeholder="Masukkan email Anda" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="error-message" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-input">
                            <input type="password" name="password" id="signInPassword" placeholder="Masukkan password Anda" required>
                            <span class="toggle-password" onclick="togglePassword('signInPassword')">👁️</span>
                        </div>
                        @error('password')
                            <span class="error-message" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="rememberMe" name="remember">
                        <label for="rememberMe">Ingat saya</label>
                    </div>
                    <button type="submit" class="btn-login">Masuk</button>
                </form>

                <div class="login-footer">
                    © 2025 Ssyar'S Intel<br>Support By WH
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>