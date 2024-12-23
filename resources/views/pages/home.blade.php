@extends('layouts.app')

<script src="{{ asset('js/success-message.js') }}" defer></script>
@section('content')
	@if (session('success'))
			<div class = "success">
				{{ session('success') }}
			</div>
		@endif
		@if (session('error'))
			<div class="error">
				{{ session('error') }}
			</div>
    @endif
	<div class = "slogan">
	<div class="slogan-container">
		<div class="slogan-container-text">
			<h1>Create <span class="highlighter">Your Own Show!</span></h1>
			<h2><span class="highlighter">Steal the Spotlight</span></h2>
			<p>And <span class="highlighter">Show</span> the World your <span class="highlighter">Talent</span></p>
			
			<a class="take-me-button" href="{{ route('events.create') }}">Get Started!</a>

		</div>
		<div class="slogan-container-image">
			<img src="{{ asset('home_dj.jpg') }}" alt="Placeholder">
		</div>
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
				<a href="{{ route('future-events') }}">
					@include('partials.show-more')
				</a>
			</div>

		<div class="purple-line"></div>

		<div class="past-events">
			<h1>Past Events: </h1>
			<div class="event-row">
				@foreach ($pastEvents->take(3) as $event)
					@include('partials.event-card', ['event' => $event])
				@endforeach
			</div>
			<a href="{{ route('past-events') }}">
			@include('partials.show-more') </a>
		</div>

		<div class="purple-line"></div>

		<div class="artists">
		<h1>Artists: </h1>
		<div class="artist-row">
			@foreach ($artists->skip(1)->take(5) as $artist)
				@include('partials.artist-card', ['artist' => $artist])
			@endforeach
		</div>
		<a href= "{{ route('artists') }}">
		@include('partials.show-more') </a>
	</div>
	</div>	
@endsection	
