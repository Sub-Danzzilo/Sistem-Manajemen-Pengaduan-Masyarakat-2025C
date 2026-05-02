import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

function togglePassword(button) {
    const input = button.previousElementSibling;
    const eyeOpen = button.querySelector('[data-eye-open]');
    const eyeClosed = button.querySelector('[data-eye-closed]');

    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';

    eyeOpen.classList.toggle('hidden', isHidden);
    eyeClosed.classList.toggle('hidden', !isHidden);

    button.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
}

window.togglePassword = togglePassword;