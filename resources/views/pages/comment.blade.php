@extends('layouts.app')

@section('content')
    <script src="{{ asset('js/app.js') }}" defer></script>

    <div class="event-page-content">
        <div class="event-page-info">

            <!-- Comment Section -->
            <div class="description-comments">
                <div class="purple-line"></div>
                <div class="event-page-comments">
                    <h1>{{ count($comments) }} Comments:</h1>
                    <div class="add-your-own-comment">
                        <img src="https://c4.wallpaperflare.com/wallpaper/380/24/860/dj-turntable-purple-music-wallpaper-preview.jpg" alt="Profile Picture" class="profile-pic">
                        <input type="text" placeholder="Add your comment..." id="new-comment" class="comment-input">
                        <button class="post-button" onclick="postComment()">Post</button>
                    </div>
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

@section('scripts')
    <script>
        function postComment() {
            const commentText = document.getElementById('new-comment').value;
            const eventId = {{ $event->event_id }};
            if (commentText) {
                axios.post("{{ route('comments.store') }}", {
                    text: commentText,
                    event_id: eventId,
                })
                .then(response => {
                    document.getElementById('new-comment').value = '';
                    const newComment = response.data.comment;
                    const commentHTML = `
                        @include('partials.comment', ['comment' => newComment])
                    `;
                    document.getElementById('comment-list').innerHTML += commentHTML;
                })
                .catch(error => {
                    console.error(error);
                    alert('Failed to post comment.');
                });
            }
        }
    </script>
@endsection
