<div class="poll-analytics-wrapper">
    <div class="poll-summary">
        <h3>{{ $poll->title }}</h3>
        <p>Total Votes: {{ $poll->calculateTotalVotes($poll->poll_id) }}</p>
    </div>

    <div class="poll-results">
        @foreach ($poll->options as $option)
            <div class="poll-result-row">
                <span class="option-name">{{ $option->name }}</span>
                <span class="option-votes">{{ $option->countVotes() }} votes</span>
                <div class="option-bar-wrapper">
                    <span class="option-percentage">{{ $option->calculatePercentage() }}%</span>
                </div>
            </div>
        @endforeach
    </div>
</div>