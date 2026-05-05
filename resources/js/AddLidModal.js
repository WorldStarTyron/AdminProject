// AddLidModal.js
// NOTE: The modal logic has been moved inline into ledenpagina.blade.php
// because it requires Blade directives: {{ route('addlid.store') }}, @csrf, and @if(session()).
// Blade directives are NOT processed inside .js files loaded via Vite.
// This file is intentionally left empty to prevent JS syntax errors.
document.addEventListener('DOMContentLoaded', function() {
        // === Modal Logic ===
        const modal = document.getElementById('addLidModal');
        const openBtn = document.getElementById('openModalBtn');
        const closeBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelModalBtn');
        const form = document.getElementById('addLidForm');
        const errorsDiv = document.getElementById('modalErrors');
        const errorList = document.getElementById('modalErrorList');
        const toast = document.getElementById('successToast');
        const submitBtn = document.getElementById('submitBtn');

        function openModal() {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = '';
            form.reset();
            errorsDiv.style.display = 'none';
            errorList.innerHTML = '';
            document.getElementById('geboortedatum').value = new Date().toISOString().split('T')[0];
        }

        openBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Close on overlay click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
        });

        // AJAX form submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Bezig...';

            const formData = new FormData(form);

            fetch("{{ route('addlid.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(response => {
                if (response.ok) {
                    closeModal();
                    toast.classList.add('show');
                    setTimeout(() => toast.classList.remove('show'), 3000);
                    setTimeout(() => window.location.reload(), 1000);
                } else if (response.status === 422) {
                    return response.json();
                } else {
                    throw new Error('Server error');
                }
            })
            .then(data => {
                if (data && data.errors) {
                    errorList.innerHTML = '';
                    Object.values(data.errors).forEach(msgs => {
                        msgs.forEach(msg => {
                            const li = document.createElement('li');
                            li.textContent = msg;
                            errorList.appendChild(li);
                        });
                    });
                    errorsDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorList.innerHTML = '<li>Er is een fout opgetreden. Probeer het opnieuw.</li>';
                errorsDiv.style.display = 'block';
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg> Lid toevoegen`;
            });
        });

        // Show toast if redirected back with success
        if(session('success'))
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        endif

        // === Client-side search ===
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                const rows = document.querySelectorAll('#ledenTable tbody .table-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }
    });