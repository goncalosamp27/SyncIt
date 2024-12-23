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

                @if ($comment->file_path)
                    @php
                        $fileExtension = pathinfo($comment->file_path, PATHINFO_EXTENSION);
                    @endphp

                    @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ asset('comment_images/' . $comment->file_path) }}" alt="Attachment" style="max-width: 200px; height: auto;">
                    @elseif (in_array($fileExtension, ['mp4', 'avi', 'mov']))
                        <video controls class="responsive-media">
                            <source src="{{ asset('storage/' . $comment->file_path) }}" type="video/{{ $fileExtension }}">
                            Your browser does not support the video tag.
                        </video>
                    @endif
                @endif

                @if(Auth::check() && Auth::id() == $comment->member_id)
                    <button class="edit-button" onclick="toggleEdit({{ $comment->member_id }})"
                        style="position: absolute; top: 10px; right: 10px; background-color: transparent; border: none; cursor: pointer; font-size: 16px;">
                        ✏️
                    </button>
                    <button id="save-button-{{ $comment->member_id }}"
                        data-update-url="{{ route('comments.update', ['comment_id' => $comment->comment_id]) }}"
                        style="position: absolute; top: 10px; right: 10px; background-color: transparent; border: none; cursor: pointer; font-size: 16px; display: none;">
                        💾
                    </button>
                    <button class="delete-button" onclick="deleteComment({{ $comment->comment_id }})"
                        style="position: absolute; top: 10px; right: 40px; background-color: transparent; border: none; cursor: pointer; font-size: 16px;">
                        🗑️
                    </button>
                @endif

                <div class="comment-date" style="font-size: smaller;">
                    <small>{{ \Carbon\Carbon::parse($comment->comment_date)->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>

        @php
            $vote = null; // Default value, in case neither upvote nor downvote is found

            if ($comment->upvotes->contains('member_id', Auth::id())) {
                $vote = true; // Upvoted
            } elseif ($comment->downvotes->contains('member_id', Auth::id())) {
                $vote = false; // Downvoted
            }
        @endphp

        @if(Auth::check() && Auth::id() != $comment->member_id)
            <div class="up-down-votes">
                <div class="upvote">
                    <button style="background-color: {{ $vote ? 'rgb(81, 154, 250)' : '#AB58FE' }}" class="upvote-button" data-comment-id="{{ $comment->comment_id }}" onclick="voteComment('upvote', this)">
                        👍<span class="count" id="upvote-count-{{ $comment->comment_id }}">
                                {{ $comment->upvotes_count }}
                        </span>
                    </button>
                </div>
                <div class="downvote">
                    <button style="background-color: {{ ($vote === false) ? 'rgb(134, 58, 58)' : '#AB58FE' }}" class="downvote-button" data-comment-id="{{ $comment->comment_id }}" onclick="voteComment('downvote', this)">
                        👎<span class="count" id="downvote-count-{{ $comment->comment_id }}">
                                {{ $comment->downvotes_count }}
                        </span>
                    </button>
                </div>
            </div>
        @endif


    </div>
@endforeach