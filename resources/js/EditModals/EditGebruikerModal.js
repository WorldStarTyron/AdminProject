// Open edit modal and fill in values
window.openEditModal = function(id, naam, email, status) {
    const form = document.getElementById('editUserForm');
    // Set form action url dynamically
    form.action = `/GebruikersBeheerPagina/${id}`;
    
    // Prefill current values
    document.getElementById('edit_naam').value = naam;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_status').value = status;
    
    // Toggle classes to show modal
    const modal = document.getElementById('editUserModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

// Close edit modal
window.closeEditModal = function() {
    const modal = document.getElementById('editUserModal');
    // Toggle classes to hide modal
    modal.classList.remove('flex');
    modal.classList.add('hidden');
};
