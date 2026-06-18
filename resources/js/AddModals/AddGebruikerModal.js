// Dit bestand zorgt voor het openen van het formulier om een nieuwe gebruiker toe te voegen

document.addEventListener('DOMContentLoaded', function (){

    // Elementen ophalen
    const openAddGebruikerModalBtn = document.getElementById('openAddGebruikerModal');
    const closeAddGebruikerModalBtn = document.getElementById('closeAddGebruikerModalBtn');
    const addGebruikerModal = document.getElementById('addGebruikerModal');
    const annuleerAddGebruikerBtn = document.getElementById('annuleerAddGebruikerBtn');

    // Modal openen
    function openAddGebruikerModal() {
        addGebruikerModal.classList.add("active");
    }

    // Modal sluiten
    function closeAddGebruikerModal() {
        addGebruikerModal.classList.remove("active");
    }

    // Knoppen koppelen
    openAddGebruikerModalBtn.addEventListener('click', openAddGebruikerModal);
    closeAddGebruikerModalBtn.addEventListener('click', closeAddGebruikerModal);
    annuleerAddGebruikerBtn.addEventListener('click', closeAddGebruikerModal);

    // Modal opnieuw openen als er fouten zijn
    if ($errors.any()) {
        openAddGebruikerModal();
    }
});
