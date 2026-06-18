// Dit bestand zorgt voor het toevoegen van een lid
// We laten het formulier zien, sturen de gegevens naar de server en zoeken in de tabel

document.addEventListener('DOMContentLoaded', function () {

    // === Stap 1: Alle elementen ophalen ===
    var modal     = document.getElementById('addLidModal');
    var openBtn   = document.getElementById('openModalBtn');
    var closeBtn  = document.getElementById('closeModalBtn');
    var cancelBtn = document.getElementById('cancelModalBtn');
    var form      = document.getElementById('addLidForm');
    var errorsDiv = document.getElementById('modalErrors');
    var errorList = document.getElementById('modalErrorList');
    var toast     = document.getElementById('successToast');
    var submitBtn = document.getElementById('submitBtn');


    // === Stap 2: Modal openen ===
    function openModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }


    // === Stap 3: Modal sluiten ===
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        form.reset();
        errorsDiv.style.display = 'none';
        errorList.innerHTML = '';

        // Vandaag als standaarddatum zetten
        var dateField = document.getElementById('geboortedatum');
        if (dateField) {
            dateField.value = new Date().toISOString().split('T')[0];
        }

        // Validatie resetten
        if (typeof window.resetFormValidation === 'function') {
            window.resetFormValidation();
        }
    }


    // === Stap 4: Knoppen koppelen ===
    if (openBtn)   openBtn.addEventListener('click', openModal);
    if (closeBtn)  closeBtn.addEventListener('click', closeModal);
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


    // === Stap 5: Formulier versturen ===
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Eerst checken of alles klopt
            if (typeof window.checkAllFields === 'function') {
                var allValid = window.checkAllFields();
                if (!allValid) {
                    return;
                }
            }

            // Knop uitschakelen tijdens versturen
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Bezig...';

            // Oude fouten verbergen
            errorsDiv.style.display = 'none';
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
            .then(async function (response) {
                if (response.ok) {
                    // Success: modal sluiten en melding tonen
                    closeModal();
                    toast.classList.add('show');
                    setTimeout(function () { toast.classList.remove('show'); }, 3000);
                    setTimeout(function () { window.location.reload(); }, 1000);
                    return null;
                }

                // JSON lezen voor foutinfo
                var json = null;
                try {
                    json = await response.json();
                } catch (err) {
                    // negeren
                }

                if (response.status === 422 && json && json.errors) {
                    return json;
                }

                // Andere fout
                var msg = (json && (json.message || (json.errors && json.errors.server && json.errors.server[0]))) ?
                    (json.message || json.errors.server[0]) : 'Server fout: ' + response.status;
                throw new Error(msg);
            })
            .then(function (data) {
                if (data && data.errors) {
                    // Foutmeldingen tonen
                    errorList.innerHTML = '';
                    var errorKeys = Object.keys(data.errors);

                    for (var i = 0; i < errorKeys.length; i++) {
                        var messages = data.errors[errorKeys[i]];
                        for (var j = 0; j < messages.length; j++) {
                            var li = document.createElement('li');
                            li.textContent = messages[j];
                            errorList.appendChild(li);
                        }
                    }

                    errorsDiv.style.display = 'block';
                }
            })
            .catch(function (error) {
                console.error('Fout:', error);
                var message = (error && error.message) ? error.message : 'Er is iets fout gegaan. Probeer opnieuw.';
                errorList.innerHTML = '<li>' + message + '</li>';
                errorsDiv.style.display = 'block';
            })
            .finally(function () {
                // Knop weer inschakelen
                submitBtn.disabled = false;
                submitBtn.innerHTML =
                    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none">' +
                    '<path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" ' +
                    'stroke-linecap="round" stroke-linejoin="round"/></svg> Lid toevoegen';
            });
        });
    }


    // === Stap 6: Zoeken in de tabel ===
    var searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var query = this.value.toLowerCase();
            var rows = document.querySelectorAll('#ledenTable tbody .table-row');

            for (var i = 0; i < rows.length; i++) {
                var text = rows[i].textContent.toLowerCase();
                if (text.indexOf(query) !== -1) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
    }

});
