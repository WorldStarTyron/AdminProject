import './bootstrap';

// sidebar menu open close + localStorage persistence
document.addEventListener('DOMContentLoaded', function() {
    // Target both `#mainSidebar` (the Tailwind layout) and `.sidebar` (class-based)
    const sidebar = document.getElementById('mainSidebar') || document.querySelector('.sidebar');
    
    if (!sidebar) return;

    // Target both `#sidebarToggle` (the actual toggle button ID) and `.menu-toggle`
    const menuToggle = document.getElementById('sidebarToggle') || document.querySelector('.menu-toggle') || sidebar.querySelector('#sidebarToggle');
    
    // Target the next sibling of `#mainSidebar` (main content container) or `.main-content`
    const mainContent = sidebar.classList.contains('fixed') ? sidebar.nextElementSibling : document.querySelector('.main-content');

    if (!mainContent) return;

    // Restore saved state from localStorage
    const savedState = localStorage.getItem('sidebar-collapsed');
    if (savedState === 'true') {
        // Apply collapsed state instantly (no transition on page load)
        sidebar.style.transition = 'none';
        mainContent.style.transition = 'none';
        
        sidebar.classList.add('collapsed');
        mainContent.classList.add('sidebar-collapsed');
        mainContent.classList.add('main-content-collapsed');

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
            mainContent.classList.toggle('main-content-collapsed');

            // Persist the current state
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
        });
    }
});
