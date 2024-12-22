<header class="navbar">
            <!-- Side Menu -->
            <div id="side-menu" class="side-menu">
            <a href="javascript:void(0)" class="close-btn" onclick="toggleMenu()">×</a>

            @if (Auth::check())
            <!-- User Info -->
				<div class="user-info">				
                    <img src="{{ Auth::user()->getProfileImage() }}" alt="Profile Picture" class="profile-pic">

                   {{-- <img src="{{ Auth::user()->getProfileImage() }}" alt="Profile Picture" class="profile-pic"> --}}

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
                <a href="javascript:void(0)" onclick="openModal(3)"><span class="delete-account">Delete Account</span></a>
            @endif
        </div>


        <div id="admin-sidebar" class="side-menu">
            <a href="javascript:void(0)" class="close-btn" onclick="toggleAdminMenu()">×</a>
            <div class="tabs">
                <a href="{{ route('admin', ['status' => 'active']) }}" > Members</a>
                <a href="{{ route('admin', ['status' => 'banned']) }}" > Banned</a>
                <a href="{{ route('admin', ['status' => 'suspended']) }}" > Suspended</a>
                <a href="{{ route('admin.reports', ['status' => 'unsolved']) }}" > Reports</a>
                <a href="{{ route('admin.reports', ['status' => 'solved']) }}" > Archive </a> 
                <a href="{{ route('create.member') }}"> New account</a>
            </div>
        </div>

    <div class="navbar-left">
        @if (Auth::check())
        <button class="menu-icon" onclick="toggleMenu()">☰</button>
        @endif

        @if(Auth::guard('admin')->check())
        <button class="menu-icon" onclick="toggleAdminMenu()">⚙️</button>

        @endif

        <div class="logo">
            <a href="{{ route('home') }}">
                <img src="{{ asset('syncit.svg') }}" alt="Logo" />
            </a>
            <div style="margin-left: 15px;">
                <a href="{{ route('events') }}" class="explore-btn">Explore Events</a>
                <a href="{{ route('artists') }}" class="explore-btn">Explore Artists</a>
            </div>
            
        </div>
    </div>

    <div class="navbar-center hidden"> <!-- Search bar initially hidden --> 
    </div>

    <div class="navbar-right">
        <div class="login-register-logout">
            @if (Auth::check() || Auth::guard('admin')->check())
            <a class="icon-button" href="{{ route('notifications') }}">✉️</a>
            <a class="button" href="{{ route('logout') }}">Logout</a>
            @else
            <a class="button" href="{{ route('login') }}">Login</a>
            <span class="register">/</span>
            <a class="button register" href="{{ route('register') }}">Register</a>
            @endif
        </div>
    </div>
</header>
