document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('checkSubscriptieBtn');
    if (!btn) return;

    btn.addEventListener('click', function () {
        const icon = document.getElementById('subscriptieIcon');
        const url = btn.getAttribute('data-url');
        const maand = btn.getAttribute('data-maand');
        const jaar = btn.getAttribute('data-jaar');

        // Zet de knop uit zodat je niet dubbel kan klikken
        btn.disabled = true;
        btn.style.opacity = '0.7';

        // Laat het icon draaien (loading animatie)
        if (icon) {
            icon.style.animation = 'spin 1s linear infinite';
        }

        // Stuur het request naar de server
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                maand: maand,
                jaar: jaar
            })
        })
        .then(response => response.json())
        .then(data => {
            // Zet de knop weer aan
            btn.disabled = false;
            btn.style.opacity = '1';
            if (icon) {
                icon.style.animation = '';
            }

            // Laat het resultaat zien in een toast
            const toast = document.getElementById('subscriptieToast');
            const text = document.getElementById('subscriptieToastText');
            if (toast && text) {
                text.textContent = data.message;
                toast.classList.add('show');

                // Toast verdwijnt na 4 seconden
                setTimeout(() => toast.classList.remove('show'), 4000);
            }

            // Pagina herladen zodat je de nieuwe status ziet in de tabel
            setTimeout(() => location.reload(), 1500);
        })
        .catch(error => {
            // Bij een fout: zet de knop weer aan
            btn.disabled = false;
            btn.style.opacity = '1';
            if (icon) {
                icon.style.animation = '';
            }

            alert('Er ging iets mis bij de subscriptie check.');
            console.error('Fout:', error);
        });
    });
});
