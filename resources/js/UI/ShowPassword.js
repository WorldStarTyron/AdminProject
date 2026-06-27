
// Werkt ook als er meerdere wachtwoordvelden op dezelfde pagina staan
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('button[id^="togglePassword"]');

    buttons.forEach(function (btn) {
        // Het input veld zit in dezelfde wrapper als de knop
        const wrapper = btn.closest('.relative') || btn.parentElement;
        const input = wrapper ? wrapper.querySelector('input') : null;

        // Iconen zoeken BINNEN de knop (lokaal scope, geen ID-conflict)
        const openEye  = btn.querySelector('.fa-eye');
        const closeEye = btn.querySelector('.fa-eye-slash');

        // Geen errors gooien als iets ontbreekt
        if (!input || !openEye || !closeEye) return;

        // Begintoestand: oog open, doorgestreepte oog verborgen
        closeEye.style.display = 'none';
        openEye.style.display  = '';

        btn.addEventListener('click', function () {
            if (input.type === 'password') {
                input.type = 'text';
                openEye.style.display  = 'none';
                closeEye.style.display = '';
            } else {
                input.type = 'password';
                openEye.style.display  = '';
                closeEye.style.display = 'none';
            }
        });
    });
});
