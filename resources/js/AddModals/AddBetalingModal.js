// Dit bestand zorgt voor het toevoegen van een betaling
// We laten het formulier zien en sturen de gegevens naar de server

document.addEventListener('DOMContentLoaded', function () {

    // === Stap 1: Alle elementen ophalen ===
    var modal = document.getElementById('addBetalingModal');
    var openBtn = document.getElementById('openBetalingModal');
    var closeBtn = document.getElementById('closeBetalingModalBtn');
    var cancelBtn = document.getElementById('cancelBetalingModalBtn');
    var form = document.getElementById('addBetalingForm');
    var errorsDiv = document.getElementById('betalingModalErrors');
    var errorList = document.getElementById('betalingModalErrorList');
    var submitBtn = document.getElementById('submitBetalingBtn');
    var fileInput = document.getElementById('betaling_bewijs');
    var fileText = document.getElementById('fileUploadText');


    // === Stap 2: Modal openen ===
    function openModal() {
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }


    // === Stap 3: Modal sluiten ===
    function closeModal() {
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
        if (form) form.reset();
        if (errorsDiv) {
            errorsDiv.classList.add('hidden');
            errorList.innerHTML = '';
        }
        if (fileText) fileText.textContent = 'Bestand uploaden';

        // Vandaag als standaarddatum zetten
        var dateField = document.getElementById('betaling_datum');
        if (dateField) {
            dateField.value = new Date().toISOString().split('T')[0];
        }
    }


    // === Stap 4: Knoppen koppelen ===
    if (openBtn) openBtn.addEventListener('click', openModal);
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


    // === Stap 5: Bestandsnaam updaten ===
    if (fileInput && fileText) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                fileText.textContent = this.files[0].name;
            } else {
                fileText.textContent = 'Bestand uploaden';
            }
        });
    }


    // === Stap 6: Formulier versturen ===
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Knop uitschakelen tijdens versturen
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Bezig...';

            // Oude fouten verbergen
            errorsDiv.classList.add('hidden');
            errorList.innerHTML = '';

            // Gegevens verzamelen
            var formData = new FormData(form);
            var storeUrl = form.getAttribute('data-store-url');

            // Versturen naar server
            fetch(storeUrl, {
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

                            // Success melding tonen
                            var toast = document.getElementById('betalingSuccessToast');
                            if (toast) {
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
                                errorsDiv.classList.remove('hidden');
                                for (var field in data.errors) {
                                    data.errors[field].forEach(function (msg) {
                                        var li = document.createElement('li');
                                        li.textContent = msg;
                                        errorList.appendChild(li);
                                    });
                                }
                            }
                        });
                    }
                })
                .catch(function () {
                    errorsDiv.classList.remove('hidden');
                    var li = document.createElement('li');
                    li.textContent = 'Er is iets fout gegaan. Probeer opnieuw.';
                    errorList.appendChild(li);
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa-solid fa-plus"></i> Betaling toevoegen';
                });
        });
    }

});
