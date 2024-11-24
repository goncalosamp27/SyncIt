


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
