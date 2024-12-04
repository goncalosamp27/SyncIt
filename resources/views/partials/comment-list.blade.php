<h1>{{ isset($comments) && is_countable($comments) ? count($comments) : 0 }} Comments:</h1>
@foreach($comments as $comment)
    <div class="comment">
        <strong>{{ $comment->member->username }}</strong>: {{ $comment->text }}
        <small>{{ $comment->comment_date }}</small>
    </div>
@endforeach


@if($comments->isEmpty())
    <p>No comments yet.</p>
@endif
