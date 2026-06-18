// Dit bestand zorgt voor het betaling bewijs
// Als je kiest voor overmaking, moet je een bestand uploaden

document.addEventListener('DOMContentLoaded', () => {
    // === Toevoegen modal ===
    const addMethodSelect = document.getElementById('betaling_methode');
    const addBewijsContainer = document.getElementById('betalingBewijsContainer');
    const addBewijsInput = document.getElementById('betaling_bewijs');
    const addFileText = document.getElementById('fileUploadText');

    if (addMethodSelect && addBewijsContainer && addBewijsInput) {
        function toggleAddBewijs() {
            if (addMethodSelect.value === 'overmaking') {
                addBewijsContainer.style.display = 'flex';
                addBewijsInput.setAttribute('required', 'required');
            } else {
                addBewijsContainer.style.display = 'none';
                addBewijsInput.removeAttribute('required');
                addBewijsInput.value = '';
                if (addFileText) addFileText.textContent = 'Bestand uploaden';
            }
        }

        addMethodSelect.addEventListener('change', toggleAddBewijs);

        const addCloseBtn = document.getElementById('closeBetalingModalBtn');
        if (addCloseBtn) {
            addCloseBtn.addEventListener('click', () => {
                addMethodSelect.value = '';
                toggleAddBewijs()
            });
        }
        const addCancelBtn = document.getElementById('cancelBetalingModalBtn');
        if (addCancelBtn) {
            addCancelBtn.addEventListener('click', () => {
                addMethodSelect.value = '';
                toggleAddBewijs()
            });
        }
    }

    // === Bewerken modal ===
    const editMethodSelect = document.getElementById('edit_betaling_methode');
    const editBewijsContainer = document.getElementById('editBetalingBewijsContainer');
    const editBewijsInput = document.getElementById('edit_betaling_bewijs');
    const editFileText = document.getElementById('editFileUploadText');

    if (editMethodSelect && editBewijsContainer && editBewijsInput) {
        function toggleEditBewijs() {
            if (editMethodSelect.value === 'overmaking') {
                editBewijsContainer.style.display = 'flex';
            } else {
                editBewijsContainer.style.display = 'none';
                editBewijsInput.value = '';
                if (editFileText) editFileText.textContent = 'Bestand uploaden';
            }
        }

        editMethodSelect.addEventListener('change', toggleEditBewijs);

        const editCloseBtn = document.getElementById('closeEditBetalingModalBtn');
        if (editCloseBtn) {
            editCloseBtn.addEventListener('click', () => {
                toggleEditBewijs();
            });
        }
        const editCancelBtn = document.getElementById('cancelEditBetalingModalBtn');
        if (editCancelBtn) {
            editCancelBtn.addEventListener('click', () => {
                toggleEditBewijs();
            });
        }

        if (editBewijsInput && editFileText) {
            editBewijsInput.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    editFileText.textContent = this.files[0].name;
                } else {
                    editFileText.textContent = 'Bestand uploaden';
                }
            });
        }

        // Tonen als bewerk modal opent
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.edit-betaling-btn');
            if (btn) {
                setTimeout(() => {
                    toggleEditBewijs();
                }, 50);
            }
        });
    }
});
