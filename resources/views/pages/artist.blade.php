@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Main Layout -->
    <div class="main-layout">
      <!-- Left Column (Personal Info) -->
      <div class="left-column">
        <div class="profile-section">
          <div class="profile-picture">
            <img src="{{ asset('storage/penguin.png') }}" alt="User Profile Picture">
          </div>
          <div class="profile-info">
            <h1>{{ $artist->member->display_name }}</h1>
            <p class="username"><span class="username">@</span>{{ $artist->member->username }}</p>
            <p class="followers-rating">
                <span class="followers-count">{{ $followersCount }}</span>
                <span class="rating-score">{{ $artist->rating }}/5.0</span>
            </p>
            <p class="followers-rating">
                <span class="followers-label">Followers</span>
                <span class="rating-label">Rating</span>
            </p>
            <p class="bio"> {{ $artist->member->bio }} </p>
            <div class="profile-tags">
              <div class="profile-music-dance">
                <span class="tag music">Music</span>
                <span class="tag dance">Dance</span>
              </div>
              <div class="profile-other-tags">
                <span class="tag">Tag 1</span>
                <span class="tag">Tag 2</span>
              </div>  
            </div>
          </div>
        </div>
      </div> 

      <!-- Right Column (Events) -->
      <div class="right-column">
        <!-- Upcoming Events -->
        <div class="events-section">
          <h2>Upcoming Events:</h2>
          <div class="events-list">
          @foreach ($artist->events->take(2) as $event)
            @if (\Carbon\Carbon::parse($event->event_date)->isFuture())
                @include('partials.event-card', ['event' => $event])
            @endif
          @endforeach

          </div>
          @include('partials.show-more')
        </div>

        <!-- Past Events -->
        <div class="events-section">
          <h2>Past Events:</h2>
          <div class="events-list">
            @foreach ($artist->events->take(2) as $event)
              @if (\Carbon\Carbon::parse($event->event_date)->isPast())
                  @include('partials.event-card', ['event' => $event])
              @endif
            @endforeach
          </div>
          @include('partials.show-more')
        </div>
      </div>
    </div>
  </div>
@endsection

