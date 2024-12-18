@extends('layouts.app')

@section('content')
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/comment.js') }}" defer></script>


    <div class="event-page-content">
        <div class="event-page-info">

            <!-- Comment Section -->
            <div class="description-comments">
                <div class="purple-line"></div>
                <div class="event-page-comments">
                    <h1>{{ count($comments) }} Comments:</h1>
                    
                    <!-- Form for Adding Comment -->
                    <form id="comment-form" method="POST">
                        @csrf
                        <div class="add-your-own-comment">
                            <img src="https://c4.wallpaperflare.com/wallpaper/380/24/860/dj-turntable-purple-music-wallpaper-preview.jpg" alt="Profile Picture" class="profile-pic">
                            <input type="text" placeholder="Add your comment..." id="new-comment" class="comment-input" required>
                            <button type="submit" class="post-button">Post</button>
                        </div>
                    </form>

                    <!-- Comment List -->
                    <div id="comment-list">
                        @foreach($comments as $comment)
                            @include('partials.comment', ['comment' => $comment])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

