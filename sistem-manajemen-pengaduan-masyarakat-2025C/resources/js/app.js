import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

function togglePassword(button){
    const input = button.previousElementSibling;
    if (input.type === "password") {
        input.type = "text";
        button.textContent = "Hide";
    } else {
        input.type = "password";
        button.textContent = "Show";
    }
}

window.togglePassword = togglePassword;