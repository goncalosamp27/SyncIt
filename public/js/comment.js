document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.getElementById('comment-form');
    const commentsContainer = document.getElementById('comments-container');

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

function postComment() {
    const commentText = document.getElementById('new-comment').value;
    const eventId = commentUrl.match(/\/event\/(\d+)\/comments/)[1]; // Extract event_id from the commentUrl

    console.log("Event ID:", eventId);
    console.log("Comment Text Retrieved:", commentText);

    if (commentText === '') {
        console.warn("Comment text is empty.");
        alert("Please write a comment.");
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log("CSRF Token Retrieved:", csrfToken);

    const requestData = { text: commentText, event_id: eventId };
    console.log("Request Data Prepared:", requestData);

    fetch(commentUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(requestData),
    })
    .then(response => {
        console.log("Fetch Response Status:", response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("Response Data:", data);

        if (data.success) {
            alert('Comment posted!');
            console.info("Comment posted successfully. Reloading the page.");
            window.location.reload(); // Reload page to show new comment
        } else {
            console.error("Server indicated failure to post comment:", data);
            alert('Failed to post comment');
        }
    })
    .catch(error => {
        console.error('Error occurred during fetch:', error);
    });
}
