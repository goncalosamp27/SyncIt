
document.addEventListener("DOMContentLoaded", function () {
    const resetButton = document.querySelector('.submit-btn'); // Correct the button selector

    resetButton.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        console.log("button was clicked");

        // Get form data
        const token = document.querySelector('input[name="token"]').value;
        const email = document.querySelector('input[name="email"]').value;
        const password = document.querySelector('#new_password').value;
        const passwordConfirmation = document.querySelector('#password_confirmation').value;
        console.log(token);
        console.log(email);
        console.log(password);

        // Validate inputs
        if (!password || !passwordConfirmation) {
            alert('Please fill out all fields.');
            return;
        }

        if (password !== passwordConfirmation) {
            alert('Passwords do not match.');
            return;
        }

        // Fetch CSRF token and date stamp
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        // Make the fetch request without using async/await
        fetch(resetPasswordUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                token: token,
                email: email,
                password: password,
                password_confirmation: passwordConfirmation,
            }),
        })
            .then(response => {
                // Handle server response
                return response.json();  // Parse JSON response
            })
            .then(data => {
                if (data.message) {
                    console.error(data.message || 'Password reset successfully!');
                } else {
                    console.error('Password reset failed.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                console.error('An error occurred while resetting the password.');
            });
    });


});
