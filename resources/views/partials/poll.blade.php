<script src="{{ asset('js/poll.js') }}"></script>
<script>
    const voteUrl = @json(route('poll.vote'));
    const pollData = @json(route('poll.data', ['poll_id' => $poll->poll_id]));
</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="poll-wrapper" data-votes="{{ json_encode($poll->options->pluck('votes')->toArray()) }}">
    <div class="poll">
        @csrf
        <!-- Poll Title -->
        <h2 class="poll-title">{{ $poll->title }}</h2>

        <input type="hidden" id="poll-id" value="{{ $poll->poll_id }}">
        <input type="hidden" id="member-id" value="{{ auth()->id() }}">

        <!-- Poll Options -->
        <div class="poll-options">
            @foreach($poll->options as $option)
                <div class="poll-option">
                    <label>
                        <input type="radio" name="poll_option_{{ $poll->poll_id }}" value="{{ $option->option_id }}"
                            data-index="{{ $loop->index }}" class="poll-option-radio">
                        {{ $option->name }}
                    </label>
                    <!-- Poll Bar for visualizing percentage -->
                    <div class="poll-bar-wrapper">
                        <div class="poll-bar" style="width: {{ $option->vote_percentage }}%;"
                            data-index="{{ $loop->index }}"></div>
                    </div>
                    <!-- Displaying the percentage -->
                    <span class="poll-percentage">{{ $option->vote_percentage }}%</span>
                </div>
            @endforeach
        </div>
        <div id="poll-errors" class="poll-errors" style="display: none;">
            <!-- Error messages will be displayed here -->
        </div>

        <!-- Poll Actions -->
        <div class="poll-actions">
            <button type="button" class="btn btn-primary submit-vote" data-poll-id="{{ $poll->poll_id }}">
                Vote
            </button>
        </div>
    </div>
</div>