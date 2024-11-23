function toggleSearchBar() {
    const searchBar = document.getElementById('search-bar-container');
    searchBar.style.display = searchBar.style.display === 'none' ? 'block' : 'none';
}


function toggleMenu() {
    const sideMenu = document.getElementById('side-menu');
    if (sideMenu.style.width === '0px' || sideMenu.style.width === '') {
        sideMenu.style.width = '250px'; // Open menu
    } else {
        sideMenu.style.width = '0'; // Close menu
    }
}