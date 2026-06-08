// EditBetalingModal.js
// ====================
// Handles the Edit Betaling modal popup:
// - Opening and closing the modal
// - Populating data from the clicked edit button
// - Submitting the PATCH request to update the payment

document.addEventListener('DOMContentLoaded', function () {

    // STEP 1: Get all elements
    var modal = document.getElementById('editBetalingModal');
    var closeBtn = document.getElementById('closeEditBetalingModalBtn');
    var cancelBtn = document.getElementById('cancelEditBetalingModalBtn');
    var form = document.getElementById('editBetalingForm');
    var errorsDiv = document.getElementById('editBetalingModalErrors');
    var errorList = document.getElementById('editBetalingModalErrorList');
    var submitBtn = document.getElementById('submitEditBetalingBtn');

    // Fields
    var idInput = document.getElementById('edit_betaling_id');
    var naamInput = document.getElementById('edit_betaling_naam');
    var datumInput = document.getElementById('edit_betaling_datum');
    var methodeInput = document.getElementById('edit_betaling_methode');
    var statusInput = document.getElementById('edit_betaling_status');
    var bedragInput = document.getElementById('edit_betaling_bedrag');

    // STEP 2: Open and Populate
    function openModal(btn) {
        if (!modal) return;

        // Retrieve data attributes
        var id = btn.getAttribute('data-id');
        var naam = btn.getAttribute('data-naam');
        var datum = btn.getAttribute('data-datum');
        var methode = btn.getAttribute('data-methode');
        var status = btn.getAttribute('data-status');
        var bedrag = btn.getAttribute('data-bedrag');

        // Populate fields
        if (idInput) idInput.value = id;
        if (naamInput) naamInput.value = naam;
        if (datumInput) datumInput.value = datum;
        if (methodeInput) methodeInput.value = methode;
        if (statusInput) statusInput.value = status;
        if (bedragInput) bedragInput.value = bedrag;

        // Reset error messages
        if (errorsDiv) {
            errorsDiv.style.display = 'none';
            errorList.innerHTML = '';
        }

        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Register click listener for all edit buttons (using delegation for pagination / updates)
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.edit-betaling-btn');
        if (btn) {
            openModal(btn);
        }
    });

    
    // STEP 3: Close the modal
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



    // STEP 4: Form submission
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var id = idInput.value;
            if (!id) return;

            // Disable submit button
            submitBtn.disabled = true;
            var originalBtnText = submitBtn.textContent;
            submitBtn.innerHTML = '<span class="spinner"></span> Bezig...';

            // Hide previous errors
            errorsDiv.style.display = 'none';
            errorList.innerHTML = '';

            // Collect form data
            var formData = new FormData(form);
            formData.append('_method', 'PUT'); // Laravel method spoofing

            // Fetch request (using POST with _method PATCH for Laravel compatibility with multipart/form-data if ever needed)
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

                            // Show success toast
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

                            // Reload page to show new values
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
                    li.textContent = 'Er is een fout opgetreden. Probeer het opnieuw.';
                    errorList.appendChild(li);
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                });
        });
    }

    

});
