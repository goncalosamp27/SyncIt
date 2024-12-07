@extends('layouts.app')

@section('content')
    <div class="admin_page">
        <div class="participants-header">

        <div class="tabs">
            <a href="{{ route('admin', ['status' => 'active']) }}" > Members</a>
            <a href="{{ route('admin', ['status' => 'banned']) }}" > Banned</a>
            <a href="{{ route('admin', ['status' => 'suspended']) }}" > Suspended</a>
            <a href="{{ route('create.member') }}"> New account</a>
        </div>

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
                    <div class="member-operations">
                        @can('viewProfile', $member)
                            <button class="view-profile-button" onclick="location.href='{{ route('artist', ['artist_id' => $member->member_id]) }}'"> View Profile </button>
                        @endcan
                        @can('isRestricted', $member)
                            <form action="{{ route('admin.remove.restriction', ['id' => $member->member_id]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button class="remove-restriction-button">Remove Restriction</button>
                            </form>
                        @else
                            <button class="edit-button" onclick="location.href='{{ route('admin.edit.member', ['id' => $member->member_id]) }}'">
                                Edit
                            </button>
                            <button class="restrict-button" data-member-id="{{ $member->member_id }}">Restrict</button>
                        @endcan
                        
                    </div>

                    <div class="restriction-modal" id="restrictionModal-{{ $member->member_id }}">
                        <div class="modal-content">
                            <span class="close-modal" id="closeModal-{{ $member->member_id }}">&times;</span>
                            <form action="{{ route('admin.restrict.member') }}" method="POST" class="restriction-form" id="restrictionForm-{{ $member->member_id }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="member_id" value="{{ $member->member_id }}">
                                <input type="hidden" name="admin_id" value="{{ Auth::guard('admin')->id() }}">

                                <label for="type-{{ $member->member_id }}">Restriction Type:</label>
                                <select name="type" id="type-{{ $member->member_id }}" class="restriction-type" data-member-id="{{ $member->member_id }}">
                                    <option value="Ban">Ban</option>
                                    <option value="Suspension">Suspension</option>
                                </select>

                                <input type="hidden" name="start" id="start-{{ $member->member_id }}" value="{{ now()->format('Y-m-d H:i:s') }}">

                                <label for="duration-{{ $member->member_id }}">Duration (Days, for suspensions only):</label>
                                <input type="number" name="duration" id="duration-{{ $member->member_id }}" min="0" class="restriction-duration" data-member-id="{{ $member->member_id }}">
                                
                                <!-- Warning Message for Duration -->
                                <p class="duration-warning" id="warning-{{ $member->member_id }}" style="color: red; display: none;">
                                    Please provide a valid duration for the suspension.
                                </p>

                                <button type="submit" class="restriction-submit" data-member-id="{{ $member->member_id }}">Apply Restriction</button>
                            </form>

                        </div>
                    </div>
            </div>
        @endforeach
    </div>
@endsection