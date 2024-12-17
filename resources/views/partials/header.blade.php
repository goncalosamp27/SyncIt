<header class="navbar">
    <div class="navbar-left">
        @if (Auth::check())
        <button class="menu-icon" onclick="toggleMenu()">☰</button>
        @endif

        @if(Auth::guard('admin')->check())
        <a class="menu-icon" href="{{ route('admin') }}">⚙️</a>
        @endif

        <div class="logo">
            <a href="{{ route('home') }}">
                <img src="{{ asset('storage/syncit.svg') }}" alt="Logo" />
            </a>
        </div>
    </div>

    <div class="navbar-center hidden"> <!-- Search bar initially hidden -->
        <form method="GET" action="{{ route('events.search') }}" class="search-bar">
            <button type="submit" class="search-button">🔍</button>
            <input type="text" name="search" placeholder="Search for events or locations..." value="{{ request('search') }}">
            <button class="search-btn" type="submit">Search</button>
        </form>
        <a href="{{ route('events') }}" class="explore-btn">Explore</a>
    </div>

    <div class="navbar-right">
        <div class="login-register-logout">
            @if (Auth::check() || Auth::guard('admin')->check())
            <a class="icon-button" href="{{ route('notifications') }}">✉️</a>
            <a class="button" href="{{ route('logout') }}">Logout</a>
            @else
            <a class="button" href="{{ route('login') }}">Login</a>
            <span>/</span>
            <a class="button" href="{{ route('register') }}">Register</a>
            @endif
        </div>
        <!-- Side Menu -->
        <div id="side-menu" class="side-menu">
            <a href="javascript:void(0)" class="close-btn" onclick="toggleMenu()">×</a>

            @if (Auth::check())
            <!-- User Info -->
				<div class="user-info">
	<!--				
                    <img src="{{ asset('storage/profiles/' . Auth::user()->profile_pic_url) }}" alt="Profile Picture" class="profile-pic">
-->
                    <img src="{{ Auth::user()->profile_pic_url }}" alt="Profile Picture" class="profile-pic">

					<h3>{{ Auth::user()->display_name }}</h3>
					<p><strong>Username:</strong> {{ Auth::user()->username }}</p>
					<p><strong>Email:</strong> {{ Auth::user()->email }}</p>
					<p><strong>Bio:</strong> {{ Auth::user()->bio }}</p>
				</div>

				<!-- Menu Links -->
				<a href="{{ route('profile.edit') }}">Edit Profile</a>
				@if(Auth::user()->isArtist(Auth::user()->member_id))
				<a href="{{ route('artist', ['artist_id' => Auth::user()->member_id]) }}">Artist page</a>
				@endif
				<a href="{{ route('tickets') }}">My Tickets</a>
				<a href="{{ route('your-events') }}">My Events</a>
                <a href="{{ route('attended-events')}}">Attended Events</a>
                <a href="{{ route('invitations')}}">Invitations</a>
				<a href="">Reset Password</a>
            @endif
        </div>
    </div>
</header>
