// Dit bestand zorgt voor de bewerk pagina van een lid
// We onthouden de zijbalk stand en gaan terug naar de ledenpagina

document.addEventListener('DOMContentLoaded', function () {

    // Zijbalk elementen
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const menuToggle = document.querySelector('.menu-toggle');

    // Zijbalk stand herstellen
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

    // Bijwerken knop
    const btnUpdate = document.getElementById('btnUpdate');
    const btnBack = document.getElementById('btnBack');
    if (btnUpdate) {
        btnUpdate.addEventListener('click', function () {
            alert('Bijwerken komt binnenkort.');
        });
    }
    // Terug knop
    if (btnBack) {
        btnBack.addEventListener('click', function () {
            window.location.href = '/ledenpagina';
        });
    }
});
