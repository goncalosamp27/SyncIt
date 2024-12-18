document.addEventListener('DOMContentLoaded', function () {

    //const commentsContainer = document.getElementById('comments-container');

    fetchComments();

    document.querySelectorAll('.comment-date').forEach(element => {
        const rawDate = element.getAttribute('data-raw-date');
        if (rawDate) {
            const formattedDate = formatDate(rawDate);
            element.querySelector('.formatted-date').textContent = formattedDate;
        }
    });

});

function fetchComments() {
    

    fetch(getCommentsUrl)
        .then(response => {

            if (!response.ok) {
                return response.json().then(data => {
                    console.error('Error fetching comments from server:', data);

                    // Log specific error details
                    console.error('Error message:', data.error);
                    console.error('Error details:', data.details);

                    throw new Error(data.error);
                });
            }
            return response.text();
        })
        .then(html => {

            const commentList = document.getElementById('comment-list');

            if (commentList) {
                commentList.innerHTML = html;
            } else {
                console.error('Comment list element not found.');
            }
        })
        .catch(error => {
            console.error('Error occurred while fetching comments:', error.message);
        });
}


function postComment(button) {
    const commentText = document.getElementById('new-comment').value;
    const eventId = button.getAttribute('data-event-id'); // Get the event ID from the button

    if (commentText.trim() === '') {
        alert("Please write a comment.");
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
                console.log("Comment was saved");
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

function voteComment(voteType, button) {
    console.log('Vote Type:', voteType, 'Button:', button); // Log voteType and button
    const commentId = button.getAttribute('data-comment-id');
    const url = `/comments/${commentId}/vote`;
    
    const voteValue = voteType === 'upvote' ? true : false;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ vote: voteValue }) 
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to vote on comment');
        }
        return response.json();
    })
    
    .then(data => {
        if (data.success) {
            const upvoteCount = document.querySelector(`#upvote-count-${commentId}`);
            const downvoteCount = document.querySelector(`#downvote-count-${commentId}`);
            upvoteCount.textContent = data.upvotes;
            downvoteCount.textContent = data.downvotes;
        }
        else{
            alert('Failed to register vote');
        }
    })
    .catch(error => {
        alert(error.message);
        console.error('Error:', error);
    });
}

