document.addEventListener('DOMContentLoaded', function (){
    
    const openAddGebruikerModalBtn = document.getElementById('openAddGebruikerModal');
    const closeAddGebruikerModalBtn = document.getElementById('closeAddGebruikerModalBtn');
    const addGebruikerModal = document.getElementById('addGebruikerModal');
    const annuleerAddGebruikerBtn = document.getElementById('annuleerAddGebruikerBtn');

    function openAddGebruikerModal() {
        addGebruikerModal.classList.add("active");
    }

    function closeAddGebruikerModal() {
        addGebruikerModal.classList.remove("active");
    }

    // EventListeners
    openAddGebruikerModalBtn.addEventListener('click', openAddGebruikerModal);
    closeAddGebruikerModalBtn.addEventListener('click', closeAddGebruikerModal);
    annuleerAddGebruikerBtn.addEventListener('click', closeAddGebruikerModal);

    // Reopen modal if validation errors exist
    if ($errors.any()) {
        openAddGebruikerModal();
    }
});