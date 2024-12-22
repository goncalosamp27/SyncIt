document.addEventListener('DOMContentLoaded', function () {
    const polls = document.querySelectorAll('.poll-wrapper'); // Get all poll wrappers

    // Loop over each poll
    polls.forEach(pollWrapper => {
        const pollOptions = pollWrapper.querySelectorAll('.poll-option-radio');
        const submitVoteButton = pollWrapper.querySelector('.submit-vote');
        const pollId = pollWrapper.querySelector('#poll-id').value;
        const memberId = pollWrapper.querySelector('#member-id').value;
        const errorContainer = pollWrapper.querySelector('.poll-errors');
        const voteUrl = `/poll-vote`;

        let votes = JSON.parse(pollWrapper.dataset.votes);
        let totalVotes = Object.values(votes).reduce((total, count) => total + count, 0);

        // Fetch poll data for this specific poll
        fetchPollData(pollId, pollWrapper);

        // Check if the user is authenticated
        if (!memberId) {
            submitVoteButton.disabled = true;
            submitVoteButton.textContent = 'Login to Vote';
            return;
        }

        // Asynchronous function to fetch poll data for the current poll
        async function fetchPollData(pollId, pollWrapper) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const pollDataUrl = `/poll-data/${pollId}`;

            try {
                const response = await fetch(pollDataUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ poll_id: pollId }),
                });

                if (response.ok) {
                    const data = await response.json();
                    votes = data.votes;
                    totalVotes = Object.values(votes).reduce((total, count) => total + count, 0);
                    updatePollBars(pollWrapper, votes, totalVotes);
                } else {
                    showError(errorContainer, 'Failed to fetch poll data. Please try again later.');
                }
            } catch (error) {
                showError(errorContainer, 'Error fetching poll data. Please try again later.');
            }
        }

        // Function to calculate the percentage of votes for each option
        function calculatePercentage(voteCount, total) {
            return total > 0 ? (voteCount / total) * 100 : 0;
        }

        // Function to update the poll bars based on the latest data
        function updatePollBars(pollWrapper, votes, totalVotes, selectedOptionId = null) {
            const allBars = pollWrapper.querySelectorAll('.poll-bar');
            const allPercentages = pollWrapper.querySelectorAll('.poll-percentage');

            pollOptions.forEach((option, index) => {
                const optionId = option.value;
                const voteCount = votes[optionId] || 0;
                const percentage = calculatePercentage(voteCount, totalVotes);

                allBars[index].style.width = percentage + '%';
                allPercentages[index].textContent = Math.round(percentage) + '%';

                // Change color based on whether the option is selected
                allBars[index].style.backgroundColor = selectedOptionId !== null && optionId == selectedOptionId
                    ? '#2ecc71'  // Green for selected option
                    : '#e74c3c';  // Red for others
            });
        }

        let selectedOptionId = null;
        // Add event listener for radio button selection
        pollOptions.forEach(option => {
            option.addEventListener('change', function () {
                selectedOptionId = this.value;
                updatePollBars(pollWrapper, votes, totalVotes, selectedOptionId);
            });
        });

        // Function to handle the "Vote" button click
        function handleVoteButtonClick(pollId, voteUrl, selectedOptionId, memberId, errorContainer) {
            hideError(errorContainer);  // Clear previous errors

            if (selectedOptionId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(voteUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        option_id: selectedOptionId,
                        poll_id: pollId,
                        member_id: memberId,
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            hideError(errorContainer);
                            fetchPollData(pollId, pollWrapper);  // Refresh the poll data after vote
                        } else {
                            if (data.message === "You have already voted for this option.") {
                                showError(errorContainer, 'You have already voted for this poll!');
                            } else {
                                showError(errorContainer, data.message || 'You cannot vote again.');
                            }
                        }
                    })
                    .catch(error => {
                        showError(errorContainer, 'An error occurred while submitting your vote. Please try again.');
                    });
            } else {
                showError(errorContainer, 'Please select an option before voting.');
            }
        }

        if (!submitVoteButton.hasAttribute('data-listener-attached')) {
            submitVoteButton.addEventListener('click', function () {
                console.log("button was clicked");
                handleVoteButtonClick(pollId, voteUrl, selectedOptionId, memberId, errorContainer);
            });

            submitVoteButton.setAttribute('data-listener-attached', 'true');
        }

        function showError(container, message) {
            container.style.display = 'block';
            container.textContent = message;

            setTimeout(function () {
                container.style.display = 'none';
            }, 3000);
        }

        function hideError(container) {
            container.style.display = 'none';
        }

        setInterval(() => fetchPollData(pollId, pollWrapper), 5000);
    });
});
