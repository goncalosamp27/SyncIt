document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.getElementById('comment-form');
    const commentsContainer = document.getElementById('comments-container');

    fetchComments();

    // Handle comment submission
    if (commentForm) {
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
    }

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

    // Fetch comments without requiring authentication
    function fetchComments() {
        fetch(`/event/${eventId}/comments`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch comments');
            }
            return response.json();
        })
        .then(data => {
            commentsContainer.innerHTML = '';
            data.comments.forEach(comment => {
                addCommentToContainer(comment);
            });
        })
        .catch(error => {
            console.error('Error fetching comments:', error);
        });
    }
});