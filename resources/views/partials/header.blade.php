<header class="navbar">
	<div class="navbar-left">
		<button class="menu-icon">☰</button>
		<div class="logo">
			<a href = "{{ url('/app')}}">
				<span class="logo-icon">🎵</span>
				<span class="logo-text">SyncIt!</span>
			</a>	
		</div>
	</div>

	<div class="navbar-center">
		<div class="search-bar">
			<span class="search-icon">🔍</span>
			<input type="text" placeholder="Search for events, artists, genres, cities..." />
		</div>
		<button class="explore-btn">Explore</button>
	</div>	

	<div class="navbar-right">
		<div class="login-register-logout">
			@if (Auth::check())
				<!-- add user options here like pfp or smth -->
				<a class="button" href="{{ url('/logout') }}">Logout</a>
			@else
				<a class="button" href="{{ url('/login') }}">Login</a>
				<span>/</span>
				<a class="button" href="{{ url('/register') }}"> Register</a>
			@endif
		</div>	
	</div>
</header>

