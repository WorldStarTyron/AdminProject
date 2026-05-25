const methodSelect = document.getElementById('betaling_methode');
const bewijsContainer = document.getElementById('betalingBewijsContainer');
const bewijsInput = document.getElementById('betaling_bewijs');

function toggleBewijsVeld() {
    if (methodSelect.value === 'overmaking') {
        bewijsContainer.style.display = 'flex';
        bewijsInput.setAttribute('required', 'required');
    } else {
        bewijsContainer.style.display = 'none';
        bewijsInput.removeAttribute('required');
        bewijsInput.value = ''; // reset bestand
        document.getElementById('fileUploadText').textContent = 'Bestand uploaden';
    }
}

// Bij verandering
methodSelect.addEventListener('change', toggleBewijsVeld);

// Reset bij sluiten modal
document.getElementById('closeBetalingModalBtn').addEventListener('click', () => {
    methodSelect.value = '';
    toggleBewijsVeld();
});
document.getElementById('cancelBetalingModalBtn').addEventListener('click', () => {
    methodSelect.value = '';
    toggleBewijsVeld();
});