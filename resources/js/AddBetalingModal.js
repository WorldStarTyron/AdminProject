// AddBetalingModal.js
// ====================
// Handles the Add Betaling modal popup:
// - Opening and closing the modal
// - File upload label update
// - Form submission placeholder (ready for backend route)

document.addEventListener('DOMContentLoaded', function () {

    // ==============================
    // STEP 1: Get all elements
    // ==============================
    var modal       = document.getElementById('addBetalingModal');
    var openBtn     = document.getElementById('openBetalingModal');
    var closeBtn    = document.getElementById('closeBetalingModalBtn');
    var cancelBtn   = document.getElementById('cancelBetalingModalBtn');
    var form        = document.getElementById('addBetalingForm');
    var errorsDiv   = document.getElementById('betalingModalErrors');
    var errorList   = document.getElementById('betalingModalErrorList');
    var submitBtn   = document.getElementById('submitBetalingBtn');
    var fileInput   = document.getElementById('betaling_bewijs');
    var fileText    = document.getElementById('fileUploadText');


    // ==============================
    // STEP 2: Open the modal
    // ==============================
    function openModal() {
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }


    // ==============================
    // STEP 3: Close the modal
    // ==============================
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
        if (fileText) fileText.textContent = 'Bestand uploaden';

        // Reset today's date as default
        var dateField = document.getElementById('betaling_datum');
        if (dateField) {
            dateField.value = new Date().toISOString().split('T')[0];
        }
    }


    // ==============================
    // STEP 4: Button click listeners
    // ==============================
    if (openBtn)   openBtn.addEventListener('click', openModal);
    if (closeBtn)  closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Close when clicking the dark overlay behind the modal
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });
    }

    // Close when pressing Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closeModal();
        }
    });


    // ==============================
    // STEP 5: File upload label update
    // ==============================
    if (fileInput && fileText) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                fileText.textContent = this.files[0].name;
            } else {
                fileText.textContent = 'Bestand uploaden';
            }
        });
    }


    // ==============================
    // STEP 6: Form submission
    // ==============================
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Bezig...';

            // Hide previous errors
            errorsDiv.style.display = 'none';
            errorList.innerHTML = '';

            // Collect form data (including file)
            var formData = new FormData(form);
            var storeUrl = form.getAttribute('data-store-url');

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

                        // Show success toast
                        var toast = document.getElementById('betalingSuccessToast');
                        if (toast) {
                            toast.classList.add('show');
                            setTimeout(function () {
                                toast.classList.remove('show');
                            }, 3000);
                        }

                        // Reload page to show new betaling in the table
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    });
                } else {
                    return response.json().then(function (data) {
                        // Show validation errors
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
                        }
                    });
                }
            })
            .catch(function () {
                errorsDiv.style.display = 'block';
                errorsDiv.classList.remove('hidden');
                var li = document.createElement('li');
                li.textContent = 'Er is een fout opgetreden. Probeer het opnieuw.';
                errorList.appendChild(li);
            })
            .finally(function () {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Betaling toevoegen';
            });
        });
    }

});
