@extends('layouts.app')

@section('content')
    <div class="admin_page">
            @php
                $status = Route::current()->parameter('status')
            @endphp
        @isset($reports)
            <h1>{{ ucfirst($status) }} reports</h1>
            @foreach($reports as $report)
                <div class="member-card">
                    <div class="member-profile-pic">
                        <img src="{{ $report->member->getProfileImage() }}" alt="{{ $report->event->event_name }}">
                    </div>
                    <div class="report-details">
                        <h3 class="member-name">{{ $report->event->event_name }} by {{'@' . $report->event->artist->member->username }}</h3>
                        <p class="member-username">Issued by {{'@' . $report->member->username }}</p>
                        <p class="message"> {{$report->message }}</p>
                    </div> 
                    <div class="report-operations">
                        <button class="view-event-button" onclick="location.href='{{ route('artist', ['artist_id' => $report->event->artist->member->member_id]) }}'"> View Profile </button>
                        <button class="view-profile-button" onclick="location.href='{{ route('event', ['event_id' => $report->event_id]) }}'"> View Event </button>
                        <form action="{{ route('reports.markSolved', ['report' => $report->report_id]) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            @if( $status == 'unsolved')
                                <button type="submit" class="report-solved-button">Mark as solved</button>
                            @else
                                <button type="submit" class="report-solved-button">Mark as unsolved</button>
                            @endif
                        </form>                    
                    </div>
                </div>
            @endforeach
            @if ($reports->isEmpty())
                @include('partials.empty')
            @endif
            <div class="pagination-container">
                {{ $reports->links('pagination::bootstrap-4') }}
            </div>
        @elseif(isset($members))
            <h1>{{ ucfirst($status) }} members</h1>

            <form method="GET" action="{{ route('members.search') }}" class="search-bar">
                <button type="submit" class="search-button">🔍</button>
                <input type="text" name="search" placeholder="Search by name or username" value="{{ request('search') }}">
                <button class="search-btn" type="submit">Search</button>
            </form>

            @foreach ($members as $member)
                <div class="member-card">
                    <div class="member-profile-pic">
                        <img src="{{ $member->getProfileImage() }}" alt="{{ $member->display_name }}">
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
                                
                                <p class="duration-warning" id="warning-{{ $member->member_id }}" style="color: red; display: none;">
                                    Please provide a valid duration for the suspension.
                                </p>

                                <button type="submit" class="restriction-submit" data-member-id="{{ $member->member_id }}">Apply Restriction</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
            @if ($members->isEmpty())
                @include('partials.empty')
            @endif
            <div class="pagination-container">
                {{ $members->links('pagination::bootstrap-4') }}
            </div>
        @endisset

    </div>
@endsection
