{!! printHeader() !!}
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>SyncIt!</title>
	@vite('resources/css/header.css')
</head>
<body>
	<header class="navbar">
		<div class="navbar-left">
			<button class="menu-icon">
			</button>
			<div class="logo">
				<span class="logo-icon">🎵</span>
				<span class="logo-text">SyncIt!</span>
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
</body>
