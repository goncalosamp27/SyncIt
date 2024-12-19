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
            @can('canInvite', $event)
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
                    <input type = "hidden" name = "invitor_id" value = "{{ Auth::user()->member_id }}">
                    <input type="hidden" name="event_id" value="{{ $event->event_id }}">
                    <button type="submit" class="search-submit-btn">Invite</button></form>
            </div>
            @endcan
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

        @if ($ticketsGrouped->isEmpty())
        <p class="no-tickets">There are no participants for this event.</p>
        @else
            @foreach ($ticketsGrouped as $memberId => $group)
                @php
                    $member = $group['member'];
                    $ticketCount = $group['ticket_count'];
                @endphp
                <div class="member-card">
                    <div class="member-profile-pic">
                        <img src="{{ $member->getProfileImage()}}" alt="{{ $member->display_name }}">
                    </div>
                    <div class="member-details">
                        <h3 class="member-name">{{ $member->display_name }}</h3>
                        <p class="member-username">{{ '@' . $member->username }}</p>
                        <p class="member-username2">Tickets: {{ $ticketCount }}</p>
                    </div>
                    @can('edit', $event)
                    <div class="member-edit">
                        <form action="{{ route('delete-participant', ['event_id' => $event->event_id, 'member_id' => $member->member_id]) }}" method="POST">
                            @csrf
                            <button class="remove-participant" title="Remove all tickets" type="submit">
                                Remove All Tickets
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            @endforeach

            <div class="pagination-container">
                {{ $ticketsGrouped->links('pagination::bootstrap-4') }}
            </div>
        @endif
        

        @can('edit', $event)
            <div class ="new-purple-line"></div>
            <div class="participants-header">
            <h1>Join Requests</h1></div>
            @if ($requests->isEmpty())
                <p class="no-tickets">There are no join requests for this event.</p>
            @else
                @foreach ($requests as $request)
                    @php
                        $member = $request->member; 
                    @endphp
                    <div class="member-card">
                        <div class="member-profile-pic">
                            <img src="{{ $member->getProfileImage() }}" alt="{{ $member->display_name }}">
                        </div>
                        <div class="member-details">
                            <h3 class="member-name">{{ $member->display_name }}</h3>
                            <p class="member-username">{{ '@' . $member->username }} wants to join your event!</p>
                        </div> 
                        <div class="member-edit">
                        <form action="/create-invitation2" method="POST">
                            @csrf
                            <input type = "hidden" name = "member_id" value = "{{ $member -> member_id }}">
                            <input type="hidden" name="event_id" value="{{ $event->event_id }}">
                            <input type = "hidden" name = "invitor_id" value = "{{ Auth::user()->member_id }}">
                            <button class="remove-participant" type="submit">
                                Invite
                            </button>
                        </form>
                        </div>
                    </div>
                @endforeach

                <div class="pagination-container">
                    {{ $requests->links('pagination::bootstrap-4') }}
                </div>

                @endcan
            @endif
    </div>
@endsection