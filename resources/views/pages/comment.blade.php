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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const commentForm = document.getElementById('comment-form');
            const commentInput = document.getElementById('new-comment');
            const commentList = document.getElementById('comment-list');
            const eventId = {{ $event->event_id }};

            // Handle comment submission
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();  // Prevent default form submission
                const commentText = commentInput.value.trim();

                if (commentText) {
                    axios.post("{{ route('comments.store') }}", {
                        text: commentText,
                        event_id: eventId,
                    })
                    .then(response => {
                        commentInput.value = '';  // Clear the input field
                        const newComment = response.data.comment;
                        
                        // Dynamically add the new comment
                        const commentHTML = `
                            <div class="comment" data-comment-id="${newComment.id}">
                                <p><strong>${newComment.user}</strong>: ${newComment.text}</p>
                                <small>${formatDate(newComment.date)}</small>
                                <button class="edit-btn" onclick="editComment(${newComment.id})">Edit</button>
                                <button class="delete-btn" onclick="deleteComment(${newComment.id})">Delete</button>
                            </div>
                        `;
                        commentList.insertAdjacentHTML('beforeend', commentHTML);  // Append to comment list
                    })
                    .catch(error => {
                        console.error(error);
                        alert('Failed to post comment.');
                    });
                }
            });

            // Function to format date
            function formatDate(dateString) {
                const date = new Date(dateString);
                return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()} ${date.getHours()}:${date.getMinutes()}`;
            }

            // Delete comment
            window.deleteComment = function(commentId) {
                if (!confirm('Are you sure you want to delete this comment?')) return;

                fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`[data-comment-id="${commentId}"]`).remove();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error deleting comment:', error));
            };

            // Edit comment (placeholder logic)
            window.editComment = function(commentId) {
                // Fetch and implement edit logic here
                alert('Edit functionality not implemented yet');
            };
        });
    </script>
@endsection
