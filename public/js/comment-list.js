function toggleEdit(commentId) {
    console.log("Edit button was clicked");
    const commentDisplay = document.querySelector(`#comment-text-${commentId} .comment-display`);
    const editTextarea = document.getElementById(`edit-textarea-${commentId}`);
    const editButton = document.querySelector(`.edit-button[onclick="toggleEdit(${commentId})"]`);
    const saveButton = document.getElementById(`save-button-${commentId}`);
    const commentDateSmall = document.querySelector(".comment-date");

    editTextarea.classList.add('comment-edit-textarea');

    if (editTextarea.style.display === "none" || editTextarea.style.display === "") {
        commentDisplay.style.display = "none";
        editTextarea.style.display = "block";
        saveButton.style.display = "inline"; 
        editButton.style.display = "none"; 
    }

    saveButton.onclick = function () {
        saveButton.style.display = "none";
        editButton.style.display = "inline";
        const newText = editTextarea.value;
        editTextarea.style.display = "none";
        commentDisplay.style.display = "block";
        commentDisplay.textContent = newText;

        const newDate = formatDate();
        commentDateSmall.innerHTML = `<small>${newDate}</small>`;
        const updateURL = saveButton.getAttribute('data-update-url');
        console.log("Retrieved Update URL:", updateURL);
        update(commentId, newText, newDate, updateURL);
    };

}

function update(commentId, newText, date, updateURL) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log(updateURL);
    console.log(date);
    const formattedDate = isNaN(new Date(date)) ? new Date().toISOString() : new Date(date).toISOString();
    fetch(updateURL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
            _method: 'PUT',
            text: newText,
            comment_date: formattedDate
        }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("Comment updated successfully!");
            } else {
                alert('Failed to update comment');
            }
        })
        .catch(error => {
            console.error('Error occurred while updating comment:', error);
        });
}

function formatDate() {
    const date = new Date();
    return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()} ${date.getHours()}:${date.getMinutes()}`;
}
