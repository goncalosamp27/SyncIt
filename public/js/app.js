function toggleMenu() {
    const sideMenu = document.getElementById('side-menu');
    if (sideMenu.style.width === '0px' || sideMenu.style.width === '') {
        if (window.innerWidth <= 400) {
            sideMenu.style.width = "100%";
        }
        else{
            sideMenu.style.width = '250px'; 
        } 
    } else {
        sideMenu.style.width = '0'; // Close menu
    }
}

function toggleAdminMenu() {
    const sideMenu = document.getElementById('admin-sidebar');
    if (sideMenu.style.width === '0px' || sideMenu.style.width === '') {
        if (window.innerWidth <= 400) {
            sideMenu.style.width = "100%";
        }
        else{
            sideMenu.style.width = '250px'; 
        } 
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

document.addEventListener('DOMContentLoaded', () => {
    const restrictButtons = document.querySelectorAll('.restrict-button');

    restrictButtons.forEach(button => {
        button.addEventListener('click', () => {
            const memberId = button.dataset.memberId;
            const modal = document.getElementById(`restrictionModal-${memberId}`); 
            modal.style.display = 'flex';

            const closeModal = modal.querySelector(`#closeModal-${memberId}`); 

            closeModal.addEventListener('click', () => {
                modal.style.display = 'none';
            });

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

window.onclick = function(event) {
    const modal = document.getElementById('deleteAccountModal');
    if (event.target === modal) {
        closeModal();
    }
}

function openPurchaseModal() {document.getElementById('purchaseModal').style.display = 'block';}

document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById('purchaseModal');
    const modalContent = document.querySelector('.new-modal-content');
    /*
    modal.addEventListener('click', function (event) {
        // Close modal only if the click is outside the modal content
        if (event.target === modal) {
            closePurchaseModal(); // Call the function to close the modal
        }
    });
    */
});

function closePurchaseModal() {
    const modal = document.getElementById('purchaseModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto'; // Re-enable body scroll if it was disabled
}
function openModal(id) {
    document.getElementById('confirmationModal' + id).style.display = 'block';
}

function closeModal(id) {
    document.getElementById('confirmationModal' + id).style.display = 'none';
}
