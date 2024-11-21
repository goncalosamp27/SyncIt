@extends('layouts.app')

@section('content')
	<div class="slogan-container">
		<div class="slogan-container-text">
			<h1>Create <span class="highlighter">Your Own Show!</span></h1>
			<h2><span class="highlighter">Steal the Spotlight</span></h2>
			<p>And <span class="highlighter">Show</span> the World your <span class="highlighter">Talent</span></p>

			@if (Auth::check())
				<a class="take-me-button" href="{{ url('/create') }}">Get Started!</a>
			@else
				<a class="take-me-button" href="{{ url('/login') }}">Get Started!</a>
			@endif	
		</div>
		<div class="slogan-container-image">
			<img src="https://c4.wallpaperflare.com/wallpaper/380/24/860/dj-turntable-purple-music-wallpaper-preview.jpg" alt="Placeholder">
		</div>
	</div>

	<div class="home-page-bottom">
		<div class="purple-line"></div>

		<div class="future-events">
			<h1>Future Events: </h1>
		</div>

		<div class="purple-line"></div>

		<div class="past-events">
			<h1>Past Events: </h1>
		</div>

		<div class="purple-line"></div>

		<div class="artists">
			<h1>Artists: </h1>
		</div>
	</div>	
@endsection	