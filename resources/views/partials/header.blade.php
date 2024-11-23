<header class="navbar">
	<div class="navbar-left">
		<button class="menu-icon" onclick="toggleMenu()">☰</button>
		<div class="logo">
			<a href="{{ route('home') }}">
				<img src="{{ asset('storage/syncit.svg') }}" alt="Logo" />
			</a>	
		</div>
	</div>

	<div class="navbar-center">
		<div class="search-bar">
			<span class="search-icon">🔍</span>
			<input type="text" placeholder="Search for events, artists, genres, cities..." />
		</div>
		<a href="{{ route('events') }}" class="explore-btn">Explore</a>
	</div>	

	<div class="navbar-right">
		<div class="login-register-logout">
			@if (Auth::check())
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
				<a href="{{ route('admin') }}">Admin Panel</a>
				<a href="{{ route('profile.edit', ['member_id' => Auth::user()->member_id]) }}">Edit Profile</a>
			@endif
		</div>
		</div>
</header>
