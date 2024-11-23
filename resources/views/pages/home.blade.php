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
			<img src="{{ asset('storage/home_dj.jpg') }}" alt="Placeholder">
		</div>
	</div> 

	<div class="home-page-bottom">
		<div class="purple-line"></div>

		<div class="future-events">
			<h1>Future Events: </h1>
			<div class="event-row">
				@foreach ($futureEvents->take(3) as $event)
					@include('partials.event-card', ['event' => $event])
				@endforeach
			</div>
			@include('partials.show-more')
		</div>

		<div class="purple-line"></div>

		<div class="past-events">
			<h1>Past Events: </h1>
			<div class="event-row">
				@foreach ($pastEvents->take(3) as $event)
					@include('partials.event-card', ['event' => $event])
				@endforeach
			</div>
			@include('partials.show-more')
		</div>

		<div class="purple-line"></div>

		<div class="artists">
		<h1>Artists: </h1>
		<div class="artist-row">
			@foreach ($artists->take(5) as $artist)
				@include('partials.artist-card', ['artist' => $artist])
			@endforeach
		</div>
		@include('partials.show-more')
	</div>
	</div>	
@endsection	