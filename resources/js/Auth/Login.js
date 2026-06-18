// Dit bestand zorgt voor het inloggen
// We laten zien dat het formulier bezig is met versturen

document.addEventListener('DOMContentLoaded', function() {

    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');

    // Als formulier er is
    if (loginForm && submitBtn) {
        loginForm.addEventListener('submit', function() {
            // Eerst checken of alles is ingevuld
            if (!loginForm.checkValidity()) {
                return;
            }

            // Knop uitschakelen en tekst veranderen
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
            if (btnText) btnText.textContent = 'Inloggen...';
        });
    }
});
