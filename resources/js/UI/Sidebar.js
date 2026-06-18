// Dit bestand zorgt voor de zijbalk
// We onthouden of de zijbalk open of dicht is

import '../bootstrap';

// Zijbalk open/dicht en opslaan
document.addEventListener('DOMContentLoaded', function() {
    // Zijbalk elementen zoeken
    const sidebar = document.getElementById('mainSidebar') || document.querySelector('.sidebar');

    if (!sidebar) return;

    // Menuknop
    const menuToggle = document.getElementById('sidebarToggle') || document.querySelector('.menu-toggle') || sidebar.querySelector('#sidebarToggle');

    // Hoofdinhoud
    const mainContent = sidebar.classList.contains('fixed') ? sidebar.nextElementSibling : document.querySelector('.main-content');

    if (!mainContent) return;

    // Opgeslagen stand herstellen
    const savedState = localStorage.getItem('sidebar-collapsed');
    if (savedState === 'true') {
        sidebar.style.transition = 'none';
        mainContent.style.transition = 'none';

        sidebar.classList.add('collapsed');
        mainContent.classList.add('sidebar-collapsed');
        mainContent.classList.add('main-content-collapsed');

        // Overgangen weer aanzetten
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                sidebar.style.transition = '';
                mainContent.style.transition = '';
            });
        });
    }

    // Knop werkt
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            mainContent.classList.toggle('main-content-collapsed');

            // Stand opslaan
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
        });
    }

    // Betalingen menu open/dicht
    const betalingDropdownToggle = document.getElementById('betalingDropdownToggle');
    const betalingDropdownMenu = document.getElementById('betalingDropdownMenu');
    const betalingDropdownChevron = document.getElementById('betalingDropdownChevron');
    const betalingDropdownContainer = document.getElementById('betalingDropdownContainer');

    if (betalingDropdownToggle && betalingDropdownMenu && betalingDropdownChevron) {
        // Stand lezen
        const isRouteActive = betalingDropdownContainer.getAttribute('data-active') === 'true';
        const savedDropdownState = localStorage.getItem('sidebar_betaling_open');

        let isOpen = isRouteActive;
        if (savedDropdownState !== null) {
            isOpen = savedDropdownState === 'true';
        }

        // Beginstand zetten
        if (isOpen) {
            betalingDropdownMenu.classList.remove('hidden');
            betalingDropdownChevron.classList.add('rotate-180');
        } else {
            betalingDropdownMenu.classList.add('hidden');
            betalingDropdownChevron.classList.remove('rotate-180');
        }

        // Open/dicht doen
        betalingDropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const currentlyOpen = !betalingDropdownMenu.classList.contains('hidden');
            if (currentlyOpen) {
                betalingDropdownMenu.classList.add('hidden');
                betalingDropdownChevron.classList.remove('rotate-180');
                localStorage.setItem('sidebar_betaling_open', 'false');
            } else {
                betalingDropdownMenu.classList.remove('hidden');
                betalingDropdownChevron.classList.add('rotate-180');
                localStorage.setItem('sidebar_betaling_open', 'true');
            }
        });
    }
});
