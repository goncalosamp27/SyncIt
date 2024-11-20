<header class="navbar">
	<div class="navbar-left">
		<button class="menu-icon">
		</button>
		<div class="logo">
			<a href = "{{ url('/app')}}">
				<span class="logo-icon">🎵</span>
				<span class="logo-text">SyncIt!</span>
			</a>	
		</div>
	</div>

	<div class="search-bar">
		<input type="text" placeholder="Search for events, artists, genres, cities..." />
		<span class="search-icon">🔍</span>
	</div>

	<div class="navbar-right">
		<button class="explore-btn">Explore</button>
		@if (Auth::check())
        	<a class="button" href="{{ url('/logout') }}">Logout</a>
        	<span>{{ Auth::user()->name }}</span>
        @else
        	<a class="button" href="{{ url('/login') }}">Login / Register</a>
        @endif
	</div>
</header>

