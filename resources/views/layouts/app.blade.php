<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ssyar\'S-Intel - Admin Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Dashboard -->
    <div id="dashboardPage" class="dashboard active">
        <nav class="navbar">
            <div class="navbar-left">
                <img src="https://i.postimg.cc/WzGLKfbL/logo-ssyar-s-intel.png" alt="Logo" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 100 100%27%3E%3Ccircle cx=%2750%27 cy=%2750%27 r=%2745%27 fill=%27%232d5016%27/%3E%3Ctext x=%2750%27 y=%2765%27 text-anchor=%27middle%27 fill=%27%23ffd700%27 font-size=%2740%27 font-weight=%27bold%27%3ESI%3C/text%3E%3C/svg%3E'">
                <div class="navbar-title">
                    <h1>Ssyar'S-Intel</h1>
                    <p>Smart Syariah Surveillance Innovation Technology</p>
                </div>
            </div>
            <div class="navbar-center">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('cameras.index') }}" class="nav-link {{ request()->routeIs('cameras*') ? 'active' : '' }}">CCTV</a>
                <a href="{{ route('events.index') }}" class="nav-link {{ request()->routeIs('events*') ? 'active' : '' }}">Pelanggaran</a>
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings*') ? 'active' : '' }}">Pengaturan</a>
            </div>
            <div class="navbar-right">
                <div class="search-box">
                    <input type="text" placeholder="Search" id="searchInput">
                    <span class="search-icon">🔍</span>
                </div>
                <div class="notification-badge" onclick="toggleNotifications()">🔔
                    @if(session('unread_notifications', 0) > 0)
                    <span class="notification-count">{{ session('unread_notifications') }}</span>
                    @endif
                    <div class="notification-dropdown" id="notificationDropdown">
                        <!-- Notifications loaded via JavaScript -->
                    </div>
                </div>
                <div class="user-info" onclick="toggleUserMenu()">
                    <div class="user-avatar" id="userAvatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                    <div class="user-name">
                        <strong id="userName">{{ Auth::user()->name }}</strong>
                        <small>{{ ucfirst(Auth::user()->role) }}</small>
                    </div>
                    <span>▼</span>
                    <div class="dropdown-menu" id="userMenu">
                        <div class="dropdown-item">👤 Profil Saya</div>
                        <div class="dropdown-item">⚙️ Pengaturan</div>
                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <div class="dropdown-item" onclick="this.parentElement.submit()">🚪 Keluar</div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        @if(session('success'))
            <div class="alert-success" style="margin: 20px; padding: 15px; background: #d4edda; color: #155724; border-radius: 8px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-danger" style="margin: 20px; padding: 15px; background: #f8d7da; color: #721c24; border-radius: 8px;">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-danger" style="margin: 20px; padding: 15px; background: #f8d7da; color: #721c24; border-radius: 8px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const API_BASE = '{{ url('/') }}';

        // Toggle User Menu
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('show');
        }

        // Toggle Notifications
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
            loadNotifications();
        }

        // Close dropdowns when clicking outside
        window.addEventListener('click', function(event) {
            if (!event.target.closest('.user-info')) {
                document.getElementById('userMenu').classList.remove('show');
            }
            if (!event.target.closest('.notification-badge')) {
                document.getElementById('notificationDropdown').classList.remove('show');
            }
        });

        // Load Notifications
        function loadNotifications() {
            // Implement notification loading from Laravel
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.innerHTML = '<div class="notification-item">Tidak ada notifikasi baru</div>';
        }

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('[class^="alert-"]').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
    @stack('scripts')
</body>
</html>