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

            // TODO: Replace with your actual store route when backend is ready
            // var storeUrl = form.getAttribute('data-store-url');
            // 
            // fetch(storeUrl, {
            //     method: 'POST',
            //     headers: {
            //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            //         'Accept': 'application/json',
            //     },
            //     body: formData,
            // })
            // .then(function (response) { ... })

            // For now, simulate success
            setTimeout(function () {
                closeModal();
                submitBtn.disabled = false;
                submitBtn.innerHTML =
                    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none">' +
                    '<path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" ' +
                    'stroke-linecap="round" stroke-linejoin="round"/></svg> Betaling toevoegen';
            }, 600);
        });
    }

});
