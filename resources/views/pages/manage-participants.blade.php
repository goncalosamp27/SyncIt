@extends('layouts.app')

@section('content')
    <div class="admin_page">
        <h1> Participants </h1>

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

            </div>
        @endforeach
    </div>
@endsection