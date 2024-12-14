document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.getElementById('comment-form');
    const commentsContainer = document.getElementById('comments-container');

    fetchComments();

    // Handle comment submission
    commentForm.addEventListener('submit', function (e) {
        e.preventDefault();  // Prevent default form submission
        const formData = new FormData(commentForm);

        // Send comment data to the backend
        fetch(`/event/${eventId}/comments`, {
            method: 'POST',
            body: formData,
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to submit comment');
            }
            return response.json();  // Assume server returns JSON
        })
        .then(data => {
            if (data.success) {
                // Update the comments container with the new comment
                addCommentToContainer(data.comment);
                commentForm.reset();  // Clear the form
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error submitting comment:', error);
        });

        commentList.addEventListener('click', function (e) {
            const target = e.target;
    
            // Check if the clicked element is a vote button
            if (target.classList.contains('upvote-button') || target.classList.contains('downvote-button')) {
                const voteType = target.classList.contains('upvote-button') ? 'up' : 'down';
                const commentId = target.getAttribute('data-comment-id');
    
                if (!commentId) {
                    console.error('No comment ID found for vote button.');
                    return;
                }
    
                console.log(`Vote Type: ${voteType}, Comment ID: ${commentId}`);
    
                // Construct the vote URL dynamically
                const voteUrl = `${voteCommentUrl}/${commentId}/vote`;
    
                // Call the voting function
                handleVote(voteType, voteUrl, commentId);
            }
        });
    
        // Handle the vote logic
        function handleVote(voteType, url, commentId) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ vote: voteType }),
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Failed to submit vote: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateVoteCounts(voteType, commentId);
                    } else {
                        alert(data.message || 'Failed to cast vote.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your vote.');
                });
        }
    
        // Update vote counts dynamically in the DOM
        function updateVoteCounts(voteType, commentId) {
            const upvoteCountElement = document.getElementById(`upvote-count-${commentId}`);
            const downvoteCountElement = document.getElementById(`downvote-count-${commentId}`);
    
            if (voteType === 'up' && upvoteCountElement) {
                upvoteCountElement.textContent = parseInt(upvoteCountElement.textContent, 10) + 1;
            } else if (voteType === 'down' && downvoteCountElement) {
                downvoteCountElement.textContent = parseInt(downvoteCountElement.textContent, 10) + 1;
            }
        }
    
    });

    // Function to dynamically add a comment to the page
    function addCommentToContainer(comment) {
        const commentHTML = createCommentHTML(comment);
        commentsContainer.insertAdjacentHTML('beforeend', commentHTML);
    }

    // Function to generate the HTML for a comment
    function createCommentHTML(comment) {
        return `
            <div class="comment" data-comment-id="${comment.id}">
                <p><strong>${comment.user}</strong>: ${comment.text}</p>
                <small>${formatDate(comment.date)}</small>
                <button class="edit-btn" onclick="editComment(${comment.id})">Edit</button>
                <button class="delete-btn" onclick="deleteComment(${comment.id})">Delete</button>
            </div>
        `;
    }

    // Function to format the date (simple example)
    function formatDate(dateString) {
        const date = new Date(dateString);
        return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()} ${date.getHours()}:${date.getMinutes()}`;
    }

    // Delete comment (example function, requires backend endpoint)
    window.deleteComment = function (commentId) {
        if (!confirm('Are you sure you want to delete this comment?')) return;

        fetch(`path/to/delete-comment.php?id=${commentId}`, {
            method: 'DELETE',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-comment-id="${commentId}"]`).remove();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error deleting comment:', error));
    };

    // Edit comment (placeholder logic)
    window.editComment = function (commentId) {
        // Fetch and implement edit logic here
        alert('Edit functionality not implemented yet');
    };
});



function postComment(button) {
    const commentText = document.getElementById('new-comment').value;
    const eventId = button.getAttribute('data-event-id'); // Get the event ID from the button

    if (commentText.trim() === '') {
        alert("Please write a commenty.");
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const requestData = { text: commentText, event_id: eventId };

    fetch(commentUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(requestData),
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                //alert('Comment posted!');
                document.getElementById('new-comment').value = ''; // Clear input
                fetchComments(); // Fetch comments dynamically
            } else {
                alert('Failed to post comment');
            }
        })
        .catch(error => {
            console.error('Error occurred:', error);
        });
}


function fetchComments() {
    const eventId = commentUrl.match(/\/event\/(\d+)\/comments/)[1]; // Extract event_id

    fetch(getCommentsUrl)
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                console.error('Error fetching comments:', data);
                throw new Error(data.error);
            });
        }
        return response.text();
    })
    .then(html => {
        document.getElementById('comment-list').innerHTML = html;
    })
    .catch(error => {
        console.error('Error occurred:', error);
    });

}

function voteComment(voteType, button) {
    const commentId = button.getAttribute('data-comment-id'); // Get the comment ID from the button's data attribute
    console.log('Vote Type:', voteType);
    console.log('Comment ID:', commentId);

    // Construct the vote URL dynamically
    const voteUrl = `/comments/${commentId}/vote`;

    // Send the vote to the server
    fetch(voteUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ vote: voteType }),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to process the vote');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Dynamically update the vote count
            const upvoteCount = document.getElementById(`upvote-count-${commentId}`);
            const downvoteCount = document.getElementById(`downvote-count-${commentId}`);

            if (voteType === 'up' && upvoteCount) {
                upvoteCount.textContent = parseInt(upvoteCount.textContent, 10) + 1;
            } else if (voteType === 'down' && downvoteCount) {
                downvoteCount.textContent = parseInt(downvoteCount.textContent, 10) + 1;
            }
        } else {
            alert(data.message || 'Failed to cast vote.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your vote.');
    });
}
