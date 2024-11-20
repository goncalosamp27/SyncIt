<head>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>
<header class="navbar">
    <div class="navbar-left">
        <button class="menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="logo">
            <a href="{{ url('/cards') }}" class="logo-text">SyncIt!</a>
        </div>
    </div>
    <div class="navbar-right">
        @if (Auth::check())
            <a class="button" href="{{ url('/logout') }}">Logout</a>
            <span>{{ Auth::user()->name }}</span>
        @else
            <a class="button" href="{{ url('/login') }}">Login / </a>
            <a class="button" href="{{ url('/login') }}">Register</a>
        @endif
    </div>
</header>
