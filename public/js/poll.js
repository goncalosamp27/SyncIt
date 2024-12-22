document.addEventListener('DOMContentLoaded', function () {
    const pollOptions = document.querySelectorAll('.poll-option-radio');
    const submitVoteButton = document.querySelector('.submit-vote');
    const pollWrapper = document.querySelector('.poll-wrapper');
    const pollId = document.getElementById('poll-id').value;
    const memberId = document.getElementById('member-id').value;
    const errorContainer = document.getElementById('poll-errors'); 

    fetchPollData();

    let votes = JSON.parse(pollWrapper.dataset.votes);
    let totalVotes = Object.values(votes).reduce((total, count) => total + count, 0);

    if (!memberId) {
        submitVoteButton.disabled = true;
        submitVoteButton.textContent = 'Login to Vote';
        return;
    }

    async function fetchPollData() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
            const response = await fetch(pollData, {
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
                updatePollBars();
            } else {
                showError('Failed to fetch poll data. Please try again later.');
            }
        } catch (error) {
            showError('Error fetching poll data. Please try again later.');
        }
    }

    function calculatePercentage(voteCount, total) {
        return total > 0 ? (voteCount / total) * 100 : 0;
    }

    function updatePollBars(selectedOptionId = null) {
        const allBars = document.querySelectorAll('.poll-bar');
        const allPercentages = document.querySelectorAll('.poll-percentage');

        pollOptions.forEach((option, index) => {
            const optionId = option.value;
            const voteCount = votes[optionId] || 0;
            const percentage = calculatePercentage(voteCount, totalVotes);

            allBars[index].style.width = percentage + '%';
            allPercentages[index].textContent = Math.round(percentage) + '%';

            allBars[index].style.backgroundColor = selectedOptionId !== null && optionId == selectedOptionId
                ? '#2ecc71'  /
                : '#e74c3c';  
        });
    }

    updatePollBars();

    let selectedOptionId = null;
    pollOptions.forEach(option => {
        option.addEventListener('change', function () {
            selectedOptionId = this.value;
            console.log('User selected option ID:', selectedOptionId);
            updatePollBars(selectedOptionId);
        });
    });

    submitVoteButton.addEventListener('click', function () {
        if (selectedOptionId) {
            console.log(selectedOptionId);
            console.log(memberId);
            console.log(pollId);
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
                        console.log("Poll was updated");
                        hideError();
                        fetchPollData();
                    } else {
                        showError('You have already voted for this poll!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('An error occurred while submitting your vote. Please try again.');
                });
        } else {
            showError('Please select an option before voting.');
        }
    });

    
    function showError(message) {
        errorContainer.style.display = 'block'; 
        errorContainer.textContent = message; 
        setTimeout(function () {
            hideError(); 
        }, 3000); 
    }


    // Function to  error messages
    function hideError() {
        errorContainer.style.display = 'none'; 
    }

    setInterval(fetchPollData, 5000);  
});
