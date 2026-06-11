import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('mainSidebar') || document.querySelector('.sidebar');
    if (!sidebar) return;

    const menuToggle = document.getElementById('sidebarToggle') || document.querySelector('.menu-toggle') || sidebar.querySelector('#sidebarToggle');
    const mainContent = sidebar.classList.contains('fixed') ? sidebar.nextElementSibling : document.querySelector('.main-content');

    if (!mainContent) return;

    const savedState = localStorage.getItem('sidebar-collapsed');
    if (savedState === 'true') {
        sidebar.style.transition = 'none';
        mainContent.style.transition = 'none';

        sidebar.classList.add('collapsed');
        mainContent.classList.add('sidebar-collapsed');
        mainContent.classList.add('main-content-collapsed');

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                sidebar.style.transition = '';
                mainContent.style.transition = '';
            });
        });

        // Tell any open dropdowns to close on initial load too
        window.dispatchEvent(new CustomEvent('sidebar-collapsed', { detail: true }));
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            mainContent.classList.toggle('main-content-collapsed');

            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);

            // Notify Alpine components (e.g. Betaling dropdown) to close
            window.dispatchEvent(new CustomEvent('sidebar-collapsed', { detail: isCollapsed }));
        });
    }
});