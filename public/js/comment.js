document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed.');

    const commentsContainer = document.getElementById('comments-container');
    console.log('Comments container:', commentsContainer);

    fetchComments();

    function fetchComments() {
        console.log('Fetching comments from:', getCommentsUrl);

        fetch(getCommentsUrl)
            .then(response => {
                console.log('Fetch response status:', response.status);

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
                console.log('Fetched comments HTML:', html);

                const commentList = document.getElementById('comment-list');
                console.log('Comment list container:', commentList);

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
