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
                    <small>{{ \Carbon\Carbon::parse($comment->comment_date)->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="up-down-votes">
        <div class="upvote">
            <button class="upvote-button" data-comment-id="{{ $comment->id }}" onclick="voteComment('up', this)">
                👍<span class="count" id="upvote-count-{{ $comment->id }}">{{ $comment->upvotes ?? 0 }}</span>
            </button>
        </div>
        <div class="downvote">
            <button class="downvote-button" data-comment-id="{{ $comment->id }}" onclick="voteComment('down', this)">
                👎<span class="count" id="downvote-count-{{ $comment->id }}">{{ $comment->downvotes ?? 0 }}</span>
            </button>
        </div>
    </div>    

@endforeach
