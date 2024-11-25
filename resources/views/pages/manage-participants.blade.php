@extends('layouts.app')

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

    <div class="admin_page">
    <div class="participants-header">
    <h1>Participants</h1>
    <div class="add-participant-container">
        <button class="add-participant-btn" onclick="toggleSearchBar()">➕</button>
        <div id="search-bar-container" class="search-bar-container">
            <form action="/create-invitation" method="POST" class="invitation-form">
                @csrf <!-- Include CSRF protection token -->
                <input 
                    type="text" 
                    name="username" 
                    id="participant-search" 
                    placeholder="Search username..." 
                    class="search-bar-input"
                    required
                />
                <textarea
                    name="message"
                    id="invitation-message"
                    placeholder="Enter a custom invitation message"
                    class="search-bar-input2"
                ></textarea>

                <input type="hidden" name="event_id" value="{{ $event->event_id }}">
                <button type="submit" class="search-submit-btn">Invite</button></form>
        </div>
    </div>  
    </div>


        
        <div class="search-bar">
            <button type="submit" class="search-button">🔍</button>
            <input 
                type="text" 
                class="search-input" 
                placeholder="Search by name, username, or email..." 
            />
        </div>

        @foreach ($participants as $participant)
            <div class="member-card">
                <div class="member-profile-pic">
                    <img src="{{ asset('storage/profiles/' . $participant->profile_pic_url) }}" alt="{{ $participant->display_name }}">
                </div>
                <div class="member-details">
                    <h3 class="member-name">{{ $participant->display_name }}</h3>
                    <p class="member-username">{{'@' . $participant->username }}</p>
                </div> 
                <div class="member-edit">
                    <button class="remove-participant" title="Remove participant">
                        Remove
                    </a>
                </div>
            </div>

        @endforeach

    </div>
@endsection