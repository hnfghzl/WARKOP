function togglePassword() {
    const password = document.getElementById('password');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    const hidden = password.type === 'password';
    password.type = hidden ? 'text' : 'password';
    eyeOpen.classList.toggle('hidden', hidden);
    eyeClosed.classList.toggle('hidden', !hidden);
}

window.togglePassword = togglePassword;
