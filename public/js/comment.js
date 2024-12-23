document.addEventListener('DOMContentLoaded', function () {

    //const commentsContainer = document.getElementById('comments-container');
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
    const fileInput = document.getElementById('file-upload'); // Get the file input element

    document.getElementById('error-new-comment').textContent = '';
    document.getElementById('error-file-upload').textContent = '';

    let hasError = false;


    if (commentText.trim() === '') {
        document.getElementById('error-new-comment').textContent = 'Please write a comment.';

        return;
    }

    const allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/avi', 'video/quicktime'];
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        if (!allowedFileTypes.includes(file.type)) {
            document.getElementById('error-file-upload').textContent = 'Invalid file format.';
            hasError = true;
        }
    }

    if (hasError) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const formData = new FormData();
    formData.append('text', commentText);
    formData.append('event_id', eventId);

    if (fileInput.files.length > 0) {
        formData.append('file', fileInput.files[0]);
    }


    const commentUrl = `/event/${eventId}/comments`;

    fetch(commentUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            if (data.errors.text) {
                document.getElementById('error-new-comment').textContent = data.errors.text[0];
            }
            if (data.errors.file) {
                document.getElementById('error-file-upload').textContent = data.errors.file[0];
            }
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log("Comment was saved");
            document.getElementById('new-comment').value = '';
            document.getElementById('file-upload').value = '';
            fetchComments();
        } else {
            alert('Failed to post comment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
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
            const upvoteButton = document.querySelector(`.upvote-button[data-comment-id="${commentId}"]`);
            const downvoteButton = document.querySelector(`.downvote-button[data-comment-id="${commentId}"]`);

            // Remove active class and reset color for both buttons
            upvoteButton.classList.remove('active');
            downvoteButton.classList.remove('active');
            upvoteButton.style.backgroundColor = '';  // Reset to default
            downvoteButton.style.backgroundColor = '';  // Reset to default

            // Add active class and set color for the selected button
            if (voteType === 'upvote') {
                upvoteButton.classList.add('active');
                upvoteButton.style.backgroundColor = 'rgb(81, 154, 250)'; // Upvote button color
                downvoteButton.style.backgroundColor = '#AB58FE'; // Opposite button color
            } else {
                downvoteButton.classList.add('active');
                downvoteButton.style.backgroundColor = 'rgb(134, 58, 58)'; // Downvote button color
                upvoteButton.style.backgroundColor = '#AB58FE'; // Opposite button color
            }

            // Update vote counts on the buttons
            const upvoteCount = document.querySelector(`#upvote-count-${commentId}`);
            const downvoteCount = document.querySelector(`#downvote-count-${commentId}`);
            upvoteCount.textContent = data.upvotes;
            downvoteCount.textContent = data.downvotes;
        } else {
            alert('Failed to register vote');
        }
    })
    .catch(error => {
        alert(error.message);
        console.error('Error:', error);
    });
}

function deleteComment(commentId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = `/comments/${commentId}`;

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(`HTTP error! Status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const commentElement = document.getElementById(`comment-${commentId}`);
            fetchComments();
            if (commentElement) {
                commentElement.remove(); // Remove the comment element from the DOM
            } else {
                console.error(`Comment element with ID comment-${commentId} not found.`);
            }
        } else {
            alert('Failed to delete comment. Please try again.');
        }
    })
    .catch(error => {
        alert(`An error occurred: ${error.message}`);
        console.error('Error:', error);
    });
}

