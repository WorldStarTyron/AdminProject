// AddLidModal.js
// ===============
// This file handles the Add Member modal popup:
// - Opening and closing the modal
// - Submitting the form via AJAX (without page reload)
// - Showing success/error messages
// - Client-side search in the members table
//
// VALIDATION is handled by FormValidator.js (separate file).

document.addEventListener('DOMContentLoaded', function () {

    // ==============================
    // STEP 1: Get all the elements we need
    // ==============================
    var modal     = document.getElementById('addLidModal');
    var openBtn   = document.getElementById('openModalBtn');
    var closeBtn  = document.getElementById('closeModalBtn');
    var cancelBtn = document.getElementById('cancelModalBtn');
    var form      = document.getElementById('addLidForm');
    var errorsDiv = document.getElementById('modalErrors');
    var errorList = document.getElementById('modalErrorList');
    var toast     = document.getElementById('successToast');
    var submitBtn = document.getElementById('submitBtn');


    // ==============================
    // STEP 2: Open the modal
    // ==============================
    function openModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // prevent scrolling behind modal
    }


    // ==============================
    // STEP 3: Close the modal
    // ==============================
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = ''; // re-enable scrolling
        form.reset();
        errorsDiv.style.display = 'none';
        errorList.innerHTML = '';

        // Reset today's date as default
        var dateField = document.getElementById('geboortedatum');
        if (dateField) {
            dateField.value = new Date().toISOString().split('T')[0];
        }

        // Reset validation styles (from FormValidator.js)
        if (typeof window.resetFormValidation === 'function') {
            window.resetFormValidation();
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
    // STEP 5: Submit the form
    // ==============================
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // stop the normal form submit

            // Run client-side validation first (from FormValidator.js)
            if (typeof window.checkAllFields === 'function') {
                var allValid = window.checkAllFields();
                if (!allValid) {
                    return; // stop if any field is invalid
                }
            }

            // Disable the button and show "loading" text
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Bezig...';

            // Hide previous errors
            errorsDiv.style.display = 'none';
            errorList.innerHTML = '';

            // Collect all form data
            var formData = new FormData(form);

            // Get the store URL from the form's data attribute
            var storeUrl = form.getAttribute('data-store-url');

            // Send to the server
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
                    // SUCCESS: close modal, show toast, reload page
                    closeModal();
                    toast.classList.add('show');
                    setTimeout(function () { toast.classList.remove('show'); }, 3000);
                    setTimeout(function () { window.location.reload(); }, 1000);
                    return null;
                } else if (response.status === 422) {
                    // VALIDATION ERROR: server found problems
                    return response.json();
                } else {
                    throw new Error('Server error');
                }
            })
            .then(function (data) {
                if (data && data.errors) {
                    // Show each error from the server
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
                console.error('Error:', error);
                errorList.innerHTML = '<li>Er is een fout opgetreden. Probeer het opnieuw.</li>';
                errorsDiv.style.display = 'block';
            })
            .finally(function () {
                // Re-enable the button
                submitBtn.disabled = false;
                submitBtn.innerHTML =
                    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none">' +
                    '<path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" ' +
                    'stroke-linecap="round" stroke-linejoin="round"/></svg> Lid toevoegen';
            });
        });
    }


    // ==============================
    // STEP 6: Client-side search in the table
    // ==============================
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