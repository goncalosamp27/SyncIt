


function toggleMenu() {
    const sideMenu = document.getElementById('side-menu');
    if (sideMenu.style.width === '0px' || sideMenu.style.width === '') {
        sideMenu.style.width = '250px'; // Open menu
    } else {
        sideMenu.style.width = '0'; // Close menu
    }
}

function toggleSearchBar() {
    const searchBarContainer = document.getElementById('search-bar-container');
    if (searchBarContainer.style.display === 'none' || searchBarContainer.style.display === '') {
        searchBarContainer.style.display = 'flex'; // Show the search bar
    } else {
        searchBarContainer.style.display = 'none'; // Hide the search bar
    }
}

// Restriction form display
document.addEventListener('DOMContentLoaded', () => {
    const restrictButtons = document.querySelectorAll('.restrict-button'); // Select all restrict buttons

    restrictButtons.forEach(button => {
        button.addEventListener('click', () => {
            const memberId = button.dataset.memberId; // Get the member ID from the button
            const modal = document.getElementById(`restrictionModal-${memberId}`); // Select the modal for this member
            modal.style.display = 'flex';

            const closeModal = modal.querySelector(`#closeModal-${memberId}`); // Select the specific close button for this modal

            // Close the modal when clicking the "X"
            closeModal.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            // Close modal when clicking outside the modal content
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    });
});

// Restriction form warning for missing duration
document.addEventListener('DOMContentLoaded', () => {
    // Get all the restriction forms on the page
    const forms = document.querySelectorAll('.restriction-form');

    forms.forEach(form => {
        const memberId = form.querySelector('input[name="member_id"]').value;

        const restrictionType = document.getElementById(`type-${memberId}`);
        const durationInput = document.getElementById(`duration-${memberId}`);
        const warningMessage = document.getElementById(`warning-${memberId}`);
        const submitButton = form.querySelector('.restriction-submit');

        // When the user changes the restriction type, enable or disable the duration field
        restrictionType.addEventListener('change', () => {
            if (restrictionType.value === 'Suspension') {
                durationInput.disabled = false;
            } else {
                durationInput.disabled = true;
                durationInput.value = ''; // Clear duration field
                warningMessage.style.display = 'none'; // Hide warning if previously displayed
            }
        });

        // On form submission, validate the duration field
        submitButton.addEventListener('click', (e) => {
            if (restrictionType.value === 'Suspension' && (!durationInput.value || durationInput.value <= 0)) {
                e.preventDefault(); // Prevent form from submitting
                warningMessage.style.display = 'block'; // Show warning
                durationInput.focus(); // Focus the duration field
            }
        });
    });
});

function openModal() {
    document.getElementById('deleteAccountModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('deleteAccountModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('deleteAccountModal');
    if (event.target === modal) {
        closeModal();
    }
}

function openModal2() {
    document.getElementById('cancelEventModal').style.display = 'block';
}

function closeModal2() {
    document.getElementById('cancelEventModal').style.display = 'none';
}