import './bootstrap';

// sidebar menu open close + localStorage persistence
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    if (!sidebar || !mainContent) return;

    // Restore saved state from localStorage
    const savedState = localStorage.getItem('sidebar-collapsed');
    if (savedState === 'true') {
        // Apply collapsed state instantly (no transition on page load)
        sidebar.style.transition = 'none';
        mainContent.style.transition = 'none';
        sidebar.classList.add('collapsed');
        mainContent.classList.add('sidebar-collapsed');

        // Re-enable transitions after the browser has painted
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                sidebar.style.transition = '';
                mainContent.style.transition = '';
            });
        });
    }

    // Toggle on click and save to localStorage
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');

            // Persist the current state
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
        });
    }
});
