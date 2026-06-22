document.addEventListener('DOMContentLoaded', function () {
    const openEyes = document.getElementById('openEyes');
    const closeEyes = document.getElementById('closeEyes');
    const password = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    togglePassword.addEventListener('click', function () {
        if (password.type === 'password') {
            password.type = 'text';
            openEyes.style.display = 'none';
            closeEyes.style.display = 'block';
        } else {
            password.type = 'password';
            openEyes.style.display = 'block';
            closeEyes.style.display = 'none';
        }
    });
});