<div class="event-comment-div">
    <div class="event-comment">
        <img src="{{ asset('storage/profiles/' . $comment->member->profile_pic_url) }}" alt="Profile Picture" class="profile-pic">
        <div class="event-comment-text">
            <div class="comment-highlighter">@{{ $comment->member->username }}:</div>
            {{ $comment->text }}
        </div>
    </div>

    <div class="up-down-votes">
        <div class="upvote">
            <button class="upvote-button" onclick="upvoteComment({{ $comment->comment_id }})">👍
                <div class="count">{{ $comment->upvotes()->count() }}</div>
            </button>
        </div>
        <div class="downvote">
            <button class="downvote-button" onclick="downvoteComment({{ $comment->comment_id }})">👎
                <div class="count">{{ $comment->downvotes()->count() }}</div>
            </button>
        </div>
        <button class="post-button" onclick="replyToComment({{ $comment->comment_id }})">Reply</button>
    </div>
</div>
