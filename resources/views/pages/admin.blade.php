@extends('layouts.app')

@section('content')
    <div class="admin_page">
        <div class="participants-header">

        <h1> Member search </h1>
        <a class="add-participant-btn" href="{{ route('create.member') }}">➕</a>
        </div>

		<form method="GET" action="{{ route('members.search') }}" class="search-bar">
				<button type="submit" class="search-button">🔍</button>
				<input type="text" name="search" placeholder="Search by name or username" value="{{ request('search') }}">
				<button class="search-btn" type="submit">Search</button>
		</form>

        @foreach ($members as $member)
            <div class="member-card">
                <div class="member-profile-pic">
                    <img src="{{ asset('storage/profiles/' . $member->profile_pic_url) }}" alt="{{ $member->display_name }}">
                </div>
                <div class="member-details">
                    <h3 class="member-name">{{ $member->display_name }}</h3>
                    <p class="member-username">{{'@' . $member->username }}</p>
                </div> 
                <div class="member-edit">
                    <a href="{{ route('admin.edit.member', ['id' => $member->member_id]) }}" class="edit-button" title="Edit">
                        ✏️
                    </a>
                    <form action="{{ route('admin.restrict.member') }}" method="POST">
                        @csrf
                        @method('PUT') <!-- This is required to spoof the PUT request -->
                        <input type="hidden" name="member_id" value="{{ $member->member_id }}">
                        <input type="hidden" name="admin_id" value="{{ Auth::guard('admin')->id() }}">
                        <label for="type">Restriction Type:</label>
                        <select name="type" id="type">
                            <option value="Ban">Ban</option>
                            <option value="Suspension">Suspension</option>
                        </select>
                        <input type="hidden" name="start" id="start" value="{{ now()->format('Y-m-d H:i:s') }}">
                        <label for="duration">Duration (Days, for suspensions only):</label>
                        <input type="integer" name="duration" id="duration" min="0">
                        <button type="submit">Apply Restriction</button>
                    </form>

                </div>
            </div>
        @endforeach
    </div>
@endsection