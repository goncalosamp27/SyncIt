<div class="event-comment-div">	
	<div class="event-comment">
		<img src="{{ asset('storage/profiles/' . $comment->user->profile_pic_url) }}" alt="Profile Picture" class="profile-pic">
		<div class="event-comment-text">
			<div class="comment-highlighter">
				@{{ $comment->user->username }} replied to <span class="comment-highlighter2">@{{ $reply->user->username }}:</span>
			</div>
			{{ $comment->content }}
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
	<!-- Button to trigger the reply form -->
	<button class="post-button" onclick="showReplyForm({{ $comment->id }})">Reply</button>
</div>	

<!-- Reply Form -->
<div id="reply-form-{{ $comment->id }}" class="reply-form" style="display:none;">
	<form action="{{ route('reply.store') }}" method="POST">
		@csrf
		<input type="hidden" name="comment_id" value="{{ $comment->id }}">
		<textarea name="reply" placeholder="Add your reply..." required></textarea>
		<button type="submit" class="submit-reply-button">Post Reply</button>
	</form>
</div>

<script>
	// Toggle visibility of the reply form
	function showReplyForm(commentId) {
		var form = document.getElementById('reply-form-' + commentId);
		form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
	}

	// Function to handle vote via AJAX
	function voteComment(voteType, commentId) {
		// Send AJAX request to upvote or downvote
		fetch("{{ url('vote') }}/" + voteType + "/" + commentId, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
			},
			body: JSON.stringify({ comment_id: commentId })
		})
		.then(response => response.json())
		.then(data => {
			// Update vote counts
			document.getElementById('upvote-count-' + commentId).textContent = data.upvotes;
			document.getElementById('downvote-count-' + commentId).textContent = data.downvotes;
		})
		.catch(error => console.error('Error:', error));
	}
</script>
