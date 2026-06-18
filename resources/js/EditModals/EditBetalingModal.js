// Dit bestand zorgt voor het bewerken van een betaling
// We vullen de gegevens in en sturen de wijzigingen naar de server

document.addEventListener('DOMContentLoaded', function () {

    // === Stap 1: Elementen ophalen ===
    var modal = document.getElementById('editBetalingModal');
    var closeBtn = document.getElementById('closeEditBetalingModalBtn');
    var cancelBtn = document.getElementById('cancelEditBetalingModalBtn');
    var form = document.getElementById('editBetalingForm');
    var errorsDiv = document.getElementById('editBetalingModalErrors');
    var errorList = document.getElementById('editBetalingModalErrorList');
    var submitBtn = document.getElementById('submitEditBetalingBtn');

    // Velden
    var idInput = document.getElementById('edit_betaling_id');
    var naamInput = document.getElementById('edit_betaling_naam');
    var datumInput = document.getElementById('edit_betaling_datum');
    var methodeInput = document.getElementById('edit_betaling_methode');
    var statusInput = document.getElementById('edit_betaling_status');
    var bedragInput = document.getElementById('edit_betaling_bedrag');


    // === Stap 2: Modal openen en invullen ===
    function openModal(btn) {
        if (!modal) return;

        // Gegevens ophalen van de knop
        var id = btn.getAttribute('data-id');
        var naam = btn.getAttribute('data-naam');
        var datum = btn.getAttribute('data-datum');
        var methode = btn.getAttribute('data-methode');
        var status = btn.getAttribute('data-status');
        var bedrag = btn.getAttribute('data-bedrag');

        // Velden vullen
        if (idInput) idInput.value = id;
        if (naamInput) naamInput.value = naam;
        if (datumInput) datumInput.value = datum;
        if (methodeInput) methodeInput.value = methode;
        if (statusInput) statusInput.value = status;
        if (bedragInput) bedragInput.value = bedrag;

        // Foutmeldingen wissen
        if (errorsDiv) {
            errorsDiv.style.display = 'none';
            errorList.innerHTML = '';
        }

        // Modal tonen
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Knip listener voor alle bewerk knoppen
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.edit-betaling-btn');
        if (btn) {
            openModal(btn);
        }
    });


    // === Stap 3: Modal sluiten ===
    function closeModal() {
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
        if (form) form.reset();
        if (errorsDiv) {
            errorsDiv.style.display = 'none';
            errorList.innerHTML = '';
        }
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Sluiten als je op de donkere achtergrond klikt
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });
    }

    // Sluiten als je op Escape drukt
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closeModal();
        }
    });



    // === Stap 4: Formulier versturen ===
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var id = idInput.value;
            if (!id) return;

            // Knop uitschakelen
            submitBtn.disabled = true;
            var originalBtnText = submitBtn.textContent;
            submitBtn.innerHTML = '<span class="spinner"></span> Bezig...';

            // Oude fouten verbergen
            errorsDiv.style.display = 'none';
            errorList.innerHTML = '';

            // Gegevens verzamelen
            var formData = new FormData(form);
            formData.append('_method', 'PUT');

            // Versturen
            var updateUrl = '/betalingen/' + id;

            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData,
            })
                .then(function (response) {
                    if (response.ok) {
                        return response.json().then(function (data) {
                            closeModal();

                            // Success melding
                            var toast = document.getElementById('betalingSuccessToast');
                            if (toast) {
                                var toastText = toast.querySelector('span');
                                if (toastText) {
                                    toastText.textContent = 'Betaling succesvol bijgewerkt!';
                                }
                                toast.classList.add('show');
                                setTimeout(function () {
                                    toast.classList.remove('show');
                                }, 3000);
                            }

                            // Pagina vernieuwen
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        });
                    } else {
                        return response.json().then(function (data) {
                            // Foutmeldingen tonen
                            if (data.errors) {
                                errorsDiv.style.display = 'block';
                                errorsDiv.classList.remove('hidden');
                                for (var field in data.errors) {
                                    data.errors[field].forEach(function (msg) {
                                        var li = document.createElement('li');
                                        li.textContent = msg;
                                        errorList.appendChild(li);
                                    });
                                }
                            } else if (data.message) {
                                errorsDiv.style.display = 'block';
                                errorsDiv.classList.remove('hidden');
                                var li = document.createElement('li');
                                li.textContent = data.message;
                                errorList.appendChild(li);
                            }
                        });
                    }
                })
                .catch(function () {
                    errorsDiv.style.display = 'block';
                    errorsDiv.classList.remove('hidden');
                    var li = document.createElement('li');
                    li.textContent = 'Er is iets fout gegaan. Probeer opnieuw.';
                    errorList.appendChild(li);
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                });
        });
    }



});
