<div class="poll-wrapper">
    <div class="poll">
        <!-- Poll Title -->
        <h2 class="poll-title">{{ $poll->title }}</h2>

        <!-- Poll Options -->
        <div class="poll-options">
            @foreach($poll->options as $option)
                <div class="poll-option">
                    <label>
                        <input type="radio" name="poll_option_{{ $poll->id }}" value="{{ $option->id }}">
                        {{ $option->name }}
                    </label>
                    @if(isset($showResults) && $showResults)
                        <div class="poll-results">
                            <div class="poll-bar" style="width: {{ $option->vote_percentage }}%;"></div>
                            <span class="poll-percentage">{{ $option->vote_percentage }}%</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Poll Actions -->
        <div class="poll-actions">
            <button type="button" class="btn btn-primary submit-vote" data-poll-id="{{ $poll->id }}">
                Vote
            </button>
        </div>
    </div>
</div>
