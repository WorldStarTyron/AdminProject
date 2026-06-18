// Dit bestand zorgt voor het invullen van de verificatiecode
// Je kunt snel naar het volgende vakje gaan

document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('[data-code-input]');

    inputs.forEach((input, index) => {
        // Naar volgende vakje gaan als je een cijfer typt
        input.addEventListener('input', (e) => {
            // Alleen cijfers toestaan
            e.target.value = e.target.value.replace(/[^0-9]/g, '');

            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        // Terug naar vorige vakje met backspace
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Alles selecteren als je in een vakje klikt
        input.addEventListener('focus', (e) => {
            e.target.select();
        });
    });
});
