document.addEventListener('DOMContentLoaded', function () {

    const commentsContainer = document.getElementById('comments-container');

    fetchComments();

    // Function to format the date
    document.querySelectorAll('.comment-date').forEach(element => {
        const rawDate = element.getAttribute('data-raw-date');
        if (rawDate) {
            const formattedDate = formatDate(rawDate);
            element.querySelector('.formatted-date').textContent = formattedDate;
        }
    });

    function formatDate(dateString) {
        const date = new Date(dateString);
        return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()} ${date.getHours()}:${date.getMinutes()}`;
    }


    // Voting functionality
    document.getElementById('comment-list').addEventListener('click', function (e) {
        console.log('Clicked element:', e.target);

        if (e.target.classList.contains('upvote-button') || e.target.classList.contains('downvote-button')) {
            e.preventDefault();
            console.warn('Voting requires login. Button clicked:', e.target);
            alert('Please log in to vote on comments.');
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

function toggleEdit(commentId) {
    console.log(`Toggling edit for comment ID: ${commentId}`); // Debug log
    const commentDisplay = document.querySelector(`#comment-text-${commentId} .comment-display`);
    const editTextarea = document.getElementById(`edit-textarea-${commentId}`);

    if (!commentDisplay || !editTextarea) {
        console.error(`Comment display or textarea not found for comment ID ${commentId}`);
        return;
    }

    if (editTextarea.style.display === "none") {
        // Switch to edit mode
        commentDisplay.style.display = "none";
        editTextarea.style.display = "block";
        editTextarea.focus();
    } else {
        // Save changes and switch back to display mode
        const newText = editTextarea.value;
        saveComment(commentId, newText);
    }
}


function saveComment(commentId, newText) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/event/${eventId}/comments/${commentId}`, { 
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ text: newText }),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to update comment');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update the displayed comment text
            const commentDisplay = document.querySelector(`#comment-text-${commentId} .comment-display`);
            const editTextarea = document.getElementById(`edit-textarea-${commentId}`);
            
            commentDisplay.textContent = newText;
            commentDisplay.style.display = "block";
            editTextarea.style.display = "none";
        } else {
            alert('Failed to update comment');
        }
    })
    .catch(error => {
        console.error('Error occurred while updating comment:', error);
    });
}
