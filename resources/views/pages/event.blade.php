@extends('layouts.app')

@section('content')
	<div class="event-page-content">
		<div class="event-page-info">
			<h1> {{ $event->event_name }} </h1>
			<a class ="user-event-owner" href="{{ url('artist/' . $event->artist->artist_id) }}" style="display: flex; align-items: center; margin-top:1rem;">
				<img 
					src="{{ asset('storage/profiles/' . $event->artist->member->profile_pic_url) }}" alt="Event Picture"
					alt="Profile Picture" 
					style="width: 5rem; height: 5rem; object-fit: cover; border-radius: 50%; margin-right: 1rem; border: 0.15rem solid white; box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.8);"
				>
				<span class ="user-event-owner-by">by: </span><h2 style="margin: 0;"> {{'@' . $event->artist->member->username}}</h2>
			</a>			
			<h3>📅 {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y - h:i A') }}</h3>
			<div class="small-line"></div>
			<h4>📍 {{ $event->location }}</h4>
			<div class="small-line"></div>
			<h5>👥 {{ $event->ticket_count }} / {{ $event->capacity }} Participants</h5>
			<a href="https://example.com" class="buy-tickets-btn" target="_blank">Get Tickets - {{ $event->price }}€</a>
		</div>

		<div class="event-page-img">
			<img src="{{ asset('storage/events/' . $event->event_media) }}" alt="Event Picture">
		</div>
	</div>
	
	<div class="description-comments">
		<div class="purple-line"></div>

		<div class="event-page-tags">
			<h1>Tags:</h1>
            @foreach ($event->tags as $tag)
			<a>
                <span class="tag-button"
                style="
                        background: #{{ $tag->color }};
                        color: #fff;
                        border-radius: 12px;
                        padding: 8px 16px;
                        display: inline-block;
                        font-weight: bold;
                        font-size: 14px;
                        text-shadow: 0px 1px 2px rgba(0, 0, 0, 0.2);
                        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
                        transition: transform 0.2s ease, box-shadow 0.2s ease;
                        ">
                {{ $tag->tag_name }}</span></a>
            @endforeach
        </div>

		<div class="purple-line"></div>
		
		<div class="event-page-description">
			<h1>Description:</h1>
				<div class="event-page-text">
					{{ $event->description }}
				</div>
		<div class="purple-line"></div>
		
		<div class="event-page-comments">
			<h1>XXX Comments:</h1>
			<div class="event-page-text">
				<div class="add-your-own-comment">
					<img src="https://c4.wallpaperflare.com/wallpaper/380/24/860/dj-turntable-purple-music-wallpaper-preview.jpg" alt="Profile Picture" class="profile-pic">
    				<input type="text" placeholder="Add your comment..." class="comment-input">
    				<button class="post-button">Post</button>
				</div>

				@include('partials.comment')
				@include('partials.reply-comment')
			</div>
		</div>
	</div>	
@endsection	