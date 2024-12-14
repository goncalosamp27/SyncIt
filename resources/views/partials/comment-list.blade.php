<h1>{{ isset($comments) && is_countable($comments) ? count($comments) : 0 }} Comments:</h1>

@foreach($comments as $comment)
    <div class="event-comment-div" style="position: relative;">    
        <div class="event-comment">
            <img src="{{ asset('storage/profiles/' . $comment->member->profile_pic_url) }}" alt="Profile Picture" class="profile-pic">
            <div class="event-comment-text">
                <div class="comment-highlighter">
                    {{ $comment->member->username }}:
                </div>

                <div id="comment-text-{{ $comment->id }}">  <!-- Comment Text with an ID for Editing -->
                    {{ $comment->text }}
                </div>

                <button class="edit-button" onclick="editComment({{ $comment->id }})" style="position: absolute; top: 10px; right: 10px; background-color: transparent; border: none; cursor: pointer; font-size: 16px;">
                    ✏️
                </button>

                <div class="comment-date" style="font-size: smaller;">
                    <small>{{ $comment->comment_date }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="up-down-votes">
        <div class="upvote">
            <button class="upvote-button" onclick="voteComment('up', {{ $comment->id }})">
                👍<div class="count" id="upvote-count-{{ $comment->id }}">{{ $comment->upvotes }}</div>
            </button>
        </div>
        <div class="downvote">
            <button class="downvote-button" onclick="voteComment('down', {{ $comment->id }})">
                👎<div class="count" id="downvote-count-{{ $comment->id }}">{{ $comment->downvotes }}</div>
            </button>
        </div>
    </div>    

@endforeach

@if($comments->isEmpty())
    <p>No comments yet.</p>
@endif

<script>
    // Toggle visibility of the reply form
    function showReplyForm(commentId) {
        var form = document.getElementById('reply-form-' + commentId);
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }

    // Function to handle vote via AJAX
    function voteComment(voteType, commentId) {
        console.log('Vote Type:', voteType);
        console.log('Comment ID:', commentId);
    fetch("{{ url('vote') }}/" + voteType + "/" + commentId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ comment_id : commentId })
    })
    .then(response => response.json())
    .then(data => {
        // Update vote counts
        document.getElementById('upvote-count-' + commentId).textContent = data.upvotes;
        document.getElementById('downvote-count-' + commentId).textContent = data.downvotes;
    })
    .catch(error => console.error('Error:', error));
}

function editComment(commentId) {
    const commentTextElement = document.getElementById('comment-text-' + commentId);
    const currentText = commentTextElement.innerText;

    commentTextElement.innerHTML = `
        <textarea id="edit-textarea-${commentId}" style="width: 100%; height: 60px;">${currentText}</textarea>
        <button onclick="saveComment(${commentId})" style="margin-top: 5px;">Save</button>
        <button onclick="cancelEdit(${commentId}, '${currentText.replace(/'/g, "\\'")}')" style="margin-top: 5px;">Cancel</button>
    `;
}

function saveComment(commentId) {
    const newText = document.getElementById('edit-textarea-' + commentId).value;

    fetch("{{ url('comments') }}/" + commentId, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ text: newText })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('comment-text-' + commentId).innerText = newText;
        } else {
            alert('Failed to update comment');
        }
    })
    .catch(error => console.error('Error:', error));
}

function cancelEdit(commentId, originalText) {
    document.getElementById('comment-text-' + commentId).innerText = originalText;
}

</script>