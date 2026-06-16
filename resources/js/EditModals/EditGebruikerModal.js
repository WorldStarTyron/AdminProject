document.addEventListener("DOMContentLoaded", function () {

    const modal = document.getElementById("editGebruikerModal");
    
function openModal(btn) {

        document.getElementById("edit_gebruiker_naam").value = btn.dataset.naam;
        document.getElementById("edit_gebruiker_email").value = btn.dataset.email;
        document.getElementById("edit_gebruiker_rol").value = btn.dataset.rol;
        document.getElementById("edit_gebruiker_status").value = btn.dataset.status;
 
        modal.classList.add("active");
    }

    function closeModal() {
        modal.classList.remove("active");
    }

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

    // Sluiten wanneer buiten het formulier wordt geklikt
    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

});

