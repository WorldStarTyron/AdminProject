
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('[data-code-input]');

            inputs.forEach((input, index) => {
                // Auto-advance to next input on keyup
                input.addEventListener('input', (e) => {
                    // Only allow digits
                    e.target.value = e.target.value.replace(/[^0-9]/g, '');

                    if (e.target.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                // Handle backspace: go to previous input
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                        inputs[index - 1].focus();
                    }
                });

                // Select all text on focus for easy overwrite
                input.addEventListener('focus', (e) => {
                    e.target.select();
                });
            });
        });
