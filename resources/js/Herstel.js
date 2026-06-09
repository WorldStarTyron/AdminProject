
    // Loop door alle herstel-knoppen op de pagina
    document.querySelectorAll('.restore-betaling-btn').forEach(function(knop) {
        knop.addEventListener('click', function() {

            // Haal het betaling_id op uit de data-id attribuut van de knop
            var id = this.dataset.id;

            // Stuur een PATCH verzoek naar de restore route
            fetch('/betalingen/' + id + '/restore', {
                method: 'PATCH',
                headers: {
                    // CSRF token is verplicht voor Laravel POST/PATCH/DELETE requests
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    // Verwijder de rij uit de tabel zonder pagina te herladen
                    knop.closest('tr').remove();
                } else {
                    // Laat een foutmelding zien als het mislukt
                    alert(data.message);
                }
            })
            .catch(function() {
                alert('Er is iets misgegaan. Probeer opnieuw.');
            });
        });
    });
