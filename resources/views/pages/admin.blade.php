@extends('layouts.app')

@section('content')
    <div class="admin_page">
        <h1> Member search </h1>

        <div class="search-bar">
            <button type="submit" class="search-button">🔍</button>
            <input 
                type="text" 
                class="search-input" 
                placeholder="Search by name, username, or email..." 
            />
        </div>

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
                </div>
            </div>
        @endforeach
    </div>
@endsection