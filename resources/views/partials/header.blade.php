<header class="navbar">
	<div class="navbar-left">
		<button class="menu-icon">☰</button>
		<div class="logo">
			<a href = "{{ route('home')}}">
				<img src="{{ asset('storage/syncit.svg') }}" alt=Logo></img>
			</a>	
		</div>
	</div>

	<div class="navbar-center">
		<div class="search-bar">
			<span class="search-icon">🔍</span>
			<input type="text" placeholder="Search for events, artists, genres, cities..." />
		</div>
		<a href="{{ route('events')}}" class="explore-btn">Explore</a>
	</div>	

	<div class="navbar-right">
		<div class="login-register-logout">
			@if (Auth::check())
				<!-- add user options here like pfp or smth -->
				<a class="button" href="{{ route('logout') }}">Logout</a>
			@else
				<a class="button" href="{{ route('login') }}">Login</a>
				<span>/</span>
				<a class="button" href="{{ route('register') }}"> Register</a>
			@endif
		</div>	
	</div>
</header>

