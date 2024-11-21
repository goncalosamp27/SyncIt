@extends('layouts.app')

@section('content')
	<div class="event-page-content">
		<div class="event-page-info">
			<h1>Title</h1>
			<div class ="user-event-owner" style="display: flex; align-items: center; margin-top:1rem;">
				<img 
					src="https://c4.wallpaperflare.com/wallpaper/380/24/860/dj-turntable-purple-music-wallpaper-preview.jpg" 
					alt="Profile Picture" 
					style="width: 5rem; height: 5rem; object-fit: cover; border-radius: 50%; margin-right: 1rem; border: 0.15rem solid white; box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.8);"
				>
				<span class ="user-event-owner-by">by: </span><h2 style="margin: 0;">USERNAME</h2>
			</div>			
			<h3>📅 INSERT DATE</h3>
			<div class="small-line"></div>
			<h4>📍 INSERT LOCATION</h4>
			<div class="small-line"></div>
			<h5>👥 XXX / YYY Participants</h5>
			<a href="https://example.com" class="buy-tickets-btn" target="_blank">Get Tickets - Price</a>
		</div>

		<div class="event-page-img">
			<img src="https://c4.wallpaperflare.com/wallpaper/380/24/860/dj-turntable-purple-music-wallpaper-preview.jpg" alt="Placeholder">
		</div>
	</div>
	
	<div class="purple-line"></div>
	
	<div class="event-page-description">
		<h1>Description:</h1>
	</div>
	<div class="purple-line"></div>
	
	<div class="event-page-comments">
		<h1>Comments:</h1>
	</div>
@endsection	