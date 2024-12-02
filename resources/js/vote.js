// Function to send an AJAX request for posting a comment
function postComment(eventId) {
    const commentText = document.getElementById('new-comment').value;
    if (commentText === '') {
        alert("Please write a comment.");
        return;
    }

    fetch(`/events/${eventId}/comments`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ comment_text: commentText }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Comment posted!');
            window.location.reload(); // Reload page to show new comment
        } else {
            alert('Failed to post comment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Function to send an AJAX request for upvoting a comment
function upvoteComment(commentId) {
    fetch(`/comments/${commentId}/upvote`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const count = document.querySelector(`#upvote-btn-${commentId} .count`);
            count.innerText = data.newUpvoteCount;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Function to send an AJAX request for downvoting a comment
function downvoteComment(commentId) {
    fetch(`/comments/${commentId}/downvote`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const count = document.querySelector(`#downvote-btn-${commentId} .count`);
            count.innerText = data.newDownvoteCount;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
