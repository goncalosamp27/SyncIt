@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Main Layout -->
    <div class="main-layout">
      <!-- Left Column (Personal Info) -->
      <div class="left-column">
        <div class="profile-section">
          <div class="profile-picture">
            <img src="https://media.gq.com/photos/5ad93798ceb93861adb912d8/16:9/w_2560%2Cc_limit/kanye-west-0814-GQ-FEKW01.01.jpg" alt="User Profile Picture">
          </div>
          <div class="profile-info">
            <h1>User Display Name</h1>
            <p class="username">@username</p>
            <p class="followers-rating">
                <span class="followers-count">2330203</span>
                <span class="rating-score">4.6/5.0</span>
            </p>
            <p class="followers-rating">
                <span class="followers-label">Followers</span>
                <span class="rating-label">Rating</span>
            </p>
            <p class="bio">
            Lorem Ipsum Is Simply Dummy Text Of The Printing And Typesetting Industry. Lorem Ipsum Has Been...
            </p>
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
            @include('partials.event-card')
          </div>
          <div class="load-more-container">
          <button class="load-more">Show me more +</button>
        </div>
        </div>

        <!-- Past Events -->
        <div class="events-section">
          <h2>Past Events:</h2>
          <div class="events-list">
            <div class="event-card"> <!-- Content similar to above --> </div>
            <div class="event-card"> <!-- Content similar to above --> </div>
          </div>
          <button class="load-more">Show me more +</button>
        </div>
      </div>
    </div>
  </div>
@endsection

