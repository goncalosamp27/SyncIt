@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Main Layout -->
    <div class="main-layout">
      <!-- Left Column (Personal Info) -->
      <div class="left-column">
        <div class="profile-section">
          <div class="profile-picture">
            @can('isBanned', $artist->member)
              <img src="{{ asset('storage/profiles/default_user.png') }}" alt="Default Profile Picture">
            @else
                <img src="{{ asset('storage/profiles/' . $artist->member->profile_pic_url) }}" alt="User Profile Picture">
            @endcan
          </div>

          <div class="profile-info">
            @can('isBanned', $artist->member)
              <h1>Anonymous</h1>
              <p class="username">@anonymous_{{ $artist->member->member_id }}</p>
              <p class="bio">This user has been banned.</p>
            @else
              <h1>{{ $artist->member->display_name }}</h1>
              <p class="username"><span class="username">@</span>{{ $artist->member->username }}</p>
              <p class="bio"> {{ $artist->member->bio }} </p>
            @endcan
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
          @foreach ($artist->events->where('event_date', '>', now())->take(2) as $event)
              @include('partials.event-card', ['event' => $event])
          @endforeach

          </div>
          <a href="{{ route('your-events') }}">
				    @include('partials.show-more')
			    </a>             
        </div>

        <!-- Past Events -->
        <div class="events-section">
          <h2>Past Events:</h2>
          <div class="events-list">
          @foreach ($artist->events->where('event_date', '<', now())->take(2) as $event)
              @include('partials.event-card', ['event' => $event])
          @endforeach
          </div>
          <a href="{{ route('your-events') }}">
				    @include('partials.show-more')
			    </a>        
        </div>
      </div>
    </div>
    @include('partials.go-back')
  </div>
@endsection

