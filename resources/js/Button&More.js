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


// ── Betaling Table dropdown ──────────────────────────────────────────────────

window.toggleDropdown = function (dropdownId, event) {
    if (event) event.stopPropagation(); // ← voorkomt dat de click naar document bubbelt

    var dropdown = document.getElementById(dropdownId);

    // Sluit alle andere dropdowns
    document.querySelectorAll('[id^="dropdown-"]').forEach(function (menu) {
        if (menu.id !== dropdownId) menu.classList.add('hidden');
    });

    dropdown.classList.toggle('hidden');
}

// Sluit dropdowns bij klik buiten
document.addEventListener('click', function (e) {
    if (!e.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(function (menu) {
            menu.classList.add('hidden');
        });
    }
});


// ── Delete betaling ──────────────────────────────────────────────────────────

window.deleteBetaling = function (id) {
    if (!confirm('Weet je zeker dat je deze betaling wilt verwijderen?')) return;

    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    fetch('/betalingPagina/delete/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        }
    })
    .then(function (response) {
        if (response.ok) {
            var toast = document.getElementById('betalingSuccessToast');
            if (toast) {
                var toastSpan = toast.querySelector('span');
                if (toastSpan) toastSpan.textContent = 'Betaling succesvol verwijderd!';
                toast.classList.add('show');
                setTimeout(function () { toast.classList.remove('show'); }, 3000);
            }
            setTimeout(function () { window.location.reload(); }, 1000);
        } else {
            alert('Er is een fout opgetreden bij het verwijderen.');
        }
    })
    .catch(function () {
        alert('Er is een netwerkfout opgetreden.');
    });
}