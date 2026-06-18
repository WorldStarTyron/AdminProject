// Dit bestand zorgt voor het bewerken van een gebruiker
// We vullen de gegevens in en tonen de modal

document.addEventListener("DOMContentLoaded", function () {

    const modal = document.getElementById("editGebruikerModal");

    // Modal openen en invullen
    function openModal(btn) {
        document.getElementById("edit_gebruiker_naam").value = btn.dataset.naam;
        document.getElementById("edit_gebruiker_email").value = btn.dataset.email;
        document.getElementById("edit_gebruiker_rol").value = btn.dataset.rol;
        document.getElementById("edit_gebruiker_status").value = btn.dataset.status;

        modal.classList.add("active");
    }

    // Modal sluiten
    function closeModal() {
        modal.classList.remove("active");
    }

    // Knoppen koppelen
    document.querySelectorAll(".openEditGebruikerModalBtn")
        .forEach(btn => {
            btn.addEventListener("click", function () {
                openModal(this);
            });
        });

    document.querySelectorAll(".closeEditGebruikerModalBtn")
        .forEach(btn => {
            btn.addEventListener("click", function () {
                closeModal(this)
            });
        });

    // Sluiten als je buiten klikt
    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

});
