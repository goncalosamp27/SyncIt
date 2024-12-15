document.addEventListener('DOMContentLoaded', function () {
    const commentsContainer = document.getElementById('comments-container');
    fetchComments();

    function fetchComments() {
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

    // Function to format the date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()} ${date.getHours()}:${date.getMinutes()}`;
    }

    // Voting functionality
    document.getElementById('comment-list').addEventListener('click', function(e) {
        if (e.target.classList.contains('upvote-button') || e.target.classList.contains('downvote-button')) {
            e.preventDefault();
            alert('Please log in to vote on comments.');
        }
    });
});
