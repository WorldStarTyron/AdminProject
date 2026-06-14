// Password visibility toggle and submit loading script
 
        document.addEventListener('DOMContentLoaded', function() {

            // Form Submit Loading state
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            if (loginForm && submitBtn) {
                loginForm.addEventListener('submit', function() {
                    // Check validity before starting loader
                    if (!loginForm.checkValidity()) {
                        return;
                    }
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
                    if (btnText) btnText.textContent = 'Logging in...';
                });
            }
        });
    