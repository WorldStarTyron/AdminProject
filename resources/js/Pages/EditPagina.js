/**
 * EditPagina.js — Basic JS for the Edit Lid page
 */
document.addEventListener('DOMContentLoaded', function () {

    // Sidebar toggle (reuse same logic as other pages)
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const menuToggle = document.querySelector('.menu-toggle');

    // Restore sidebar state from localStorage
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar?.classList.add('collapsed');
        mainContent?.classList.add('sidebar-collapsed');
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }

    // Update button — placeholder for future update modal
    const btnUpdate = document.getElementById('btnUpdate');
    const btnBack = document.getElementById('btnBack');
    if (btnUpdate) {
        btnUpdate.addEventListener('click', function () {
            alert('Update functionaliteit wordt binnenkort toegevoegd.');
        });
    }
    // back button
    if (btnBack) {
        btnBack.addEventListener('click', function () {
            window.location.href = '/ledenpagina';
        });
    }
});
