
        function updateSelectie() {
            const checkboxes = document.querySelectorAll('.openstaand-checkbox:checked');
            let totaal = 0;
            checkboxes.forEach(c => totaal += parseFloat(c.dataset.bedrag || 0));

            document.getElementById('selectieCount').textContent = checkboxes.length + ' maand' + (checkboxes.length === 1 ? '' : 'en');
            document.getElementById('subtotaal').textContent = totaal.toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Open de bestandskiezer, maar vraag eerst om een maandselectie als er openstaande maanden zijn
        function kiesBestand() {
            const heeftOpenstaand = document.querySelectorAll('.openstaand-checkbox').length > 0;
            const selectie = document.querySelectorAll('.openstaand-checkbox:checked').length;
            if (heeftOpenstaand && selectie === 0) {
                alert('Selecteer eerst de maand(en) die u wilt betalen.');
                return;
            }
            document.getElementById('bewijsInput').click();
        }

        // Zet de gekozen maanden als hidden inputs in het formulier en verstuur
        function submitBewijs() {
            const form = document.getElementById('bewijsForm');
            form.querySelectorAll('input[name="betaling_ids[]"]').forEach(e => e.remove());
            document.querySelectorAll('.openstaand-checkbox:checked').forEach(c => {
                const h = document.createElement('input');
                h.type = 'hidden';
                h.name = 'betaling_ids[]';
                h.value = c.dataset.id;
                form.appendChild(h);
            });
            form.submit();
        }
