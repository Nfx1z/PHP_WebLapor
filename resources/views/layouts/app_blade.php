<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'YOLO Action Dashboard')</title>
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .alert {
            padding: 12px 20px;
            margin: 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="topbar">
        <div class="logo">
            <h1>YOLO Action Dashboard</h1>
            <p>Islamic Behavior Detection System</p>
        </div>
        
        <nav>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard*') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('cameras.index') }}" class="{{ request()->routeIs('cameras*') ? 'active' : '' }}">Cameras</a>
            <a href="{{ route('events.index') }}" class="{{ request()->routeIs('events*') ? 'active' : '' }}">Events</a>
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings*') ? 'active' : '' }}">Settings</a>
        </nav>

        <div class="search-user">
            <div class="user-dropdown">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="user-info">
                    <span>{{ Auth::user()->name }}</span>
                    <small>{{ Auth::user()->role }}</small>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: white; cursor: pointer; padding: 8px 16px;">Logout</button>
            </form>
        </div>
    </header>

    <main>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        // Set CSRF token for all AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
    @stack('scripts')
</body>
</html>