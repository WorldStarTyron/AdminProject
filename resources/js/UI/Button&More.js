// Dit bestand zorgt voor de "meer" knop met een uitklapmenu
// Als je op de knop klikt, klapt het menu uit

document.addEventListener('DOMContentLoaded', () => {
    const btnMore = document.querySelectorAll('.btn-more');
    const dropdownMenus = document.querySelectorAll('.dropdown-menu');

    btnMore.forEach((btn, index) => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();

            dropdownMenus.forEach((menu, i) => {
                if (i !== index) menu.classList.add('hidden');
            });

            dropdownMenus[index].classList.toggle('hidden');
        });
    });

    dropdownMenus.forEach(menu => {
        menu.addEventListener('click', (e) => e.stopPropagation());
    });
});
