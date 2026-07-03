@extends('layouts.app')

@section('content')
    <div class="edit-page">
        <h1>Edit Member</h1>
        <form action="{{ route('member.updates', ['id' => $member->member_id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- For updating -->

            <!-- Display Name -->
            <div class="form-group">
                <label for="display_name">Display Name:</label>
                <input 
                    type="text" 
                    id="display_name" 
                    name="display_name" 
                    value="{{ $member->display_name }}" 
                    required 
                />
            </div>

            <!-- Username -->
            <div class="form-group">
                <label for="username">Username:</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="{{ $member->username }}" 
                    required 
                />
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email:</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ $member->email }}" 
                    required 
                />
            </div>

            <!-- Profile Picture -->
            <div class="form-group">
                <label for="profile_pic">Profile Picture:</label>
                <input type="file" id="profile_pic" name="profile_pic_url" />
            </div>

            <!-- Bio -->
            <div class="form-group">
                <label for="bio">Bio:</label>
                <textarea id="bio" name="bio">{{ $member->bio }}</textarea>
            </div>

            <!-- Submit -->
            <button type="submit" class="save-button">Save Changes</button>

            <!-- Discard Changes Button -->
            <a href="{{ route('admin', ['status' => 'active']) }}" class="discard-button">Discard Changes</a>

        </form>
    </div>
@endsection
