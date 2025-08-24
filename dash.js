document.addEventListener('DOMContentLoaded', () => {
    const menuIcon = document.querySelector('.menu-icon');
    const sidebar = document.querySelector('.sidebar');

    // Add event listener to the menu icon to toggle the 'hidden' class on the sidebar
    menuIcon.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
    });
});
