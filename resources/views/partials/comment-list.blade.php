@foreach($comments as $comment)
    <script src="{{ asset('js/comment-list.js') }}" defer></script>
    <div class="event-comment-div" style="position: relative;">
        <div class="event-comment">
            <img src="{{ $comment->member->getProfileImage() }}" alt="Profile Picture"
                class="profile-pic">
            <div class="event-comment-text">
                <div class="comment-highlighter">
                    {{ $comment->member->username }}:
                </div>

                <div id="comment-text-{{ $comment->member_id }}">
                    <span class="comment-display">{{ $comment->text }}</span>
                    <textarea id="edit-textarea-{{ $comment->member_id }}"
                        style="display:none;">{{ $comment->text }}</textarea>
                </div>
                

                @if(Auth::check() && Auth::id() !== $comment->member_id)
                    <button class="edit-button" onclick="toggleEdit({{ $comment->member_id }})"
                        style="position: absolute; top: 10px; right: 10px; background-color: transparent; border: none; cursor: pointer; font-size: 16px;">
                        ✏️
                    </button>
                    <button id="save-button-{{ $comment->member_id }}"
                        data-update-url="{{ route('comments.update', ['comment_id' => $comment->comment_id]) }}"
                        style="position: absolute; top: 10px; right: 10px; background-color: transparent; border: none; cursor: pointer; font-size: 16px; display: none;">
                        💾
                    </button>
                @endif

                <div class="comment-date" style="font-size: smaller;">
                    <small>{{ \Carbon\Carbon::parse($comment->comment_date)->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>

        @if(Auth::check() && Auth::id() != $comment->member_id)
            <div class="up-down-votes">
                <div class="upvote">
                    <button class="upvote-button" data-comment-id="{{ $comment->comment_id }}" onclick="voteComment('upvote', this)">
                        👍<span class="count" id="upvote-count-{{ $comment->comment_id }}">
                            @if(!empty($comment->upvotes_count) && $comment->upvotes_count > 0)
                                {{ $comment->upvotes_count }}
                            @endif
                        </span>
                    </button>
                </div>
                <div class="downvote">
                    <button class="downvote-button" data-comment-id="{{ $comment->comment_id }}" onclick="voteComment('downvote', this)">
                        👎<span class="count" id="downvote-count-{{ $comment->comment_id }}">
                            @if(!empty($comment->downvotes_count) && $comment->downvotes_count > 0)
                                {{ $comment->downvotes_count }}
                            @endif
                        </span>
                    </button>
                </div>
            </div>
        @endif


    </div>
@endforeach