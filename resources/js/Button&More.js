document.addEventListener('DOMContentLoaded', () => {
    const btnMore = document.querySelectorAll('.btn-more');
    const dropdownMenus = document.querySelectorAll('.dropdown-menu');

    btnMore.forEach((btn, index) => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();

            // Close all other dropdowns first
            dropdownMenus.forEach((menu, i) => {
                if (i !== index) {
                    menu.classList.add('hidden');
                }
            });

            // Toggle the clicked dropdown
            dropdownMenus[index].classList.toggle('hidden');
        });
    });

    // Close all dropdowns when clicking outside
    document.addEventListener('click', () => {
        dropdownMenus.forEach(menu => {
            menu.classList.add('hidden');
        });
    });

    // Prevent clicks inside the dropdown from closing it
    dropdownMenus.forEach(menu => {
        menu.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });
});