// Validación en tiempo real para el campo de contraseña en el formulario de crear usuario

document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const passwordHelp = document.getElementById('password-help');
    const passwordConfirmHelp = document.getElementById('password-confirm-help');

    function validatePassword(password) {
        const minLength = password.length >= 8;
        const hasLower = /[a-z]/.test(password);
        const hasUpper = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[@$!%*#?&._-]/.test(password);
        return { minLength, hasLower, hasUpper, hasNumber, hasSpecial };
    }

    function updateHelp(password) {
        const v = validatePassword(password);
        passwordHelp.innerHTML = `
            <ul>
                <li style="color:${v.minLength ? 'green' : 'red'}">Mínimo 8 caracteres</li>
                <li style="color:${v.hasLower ? 'green' : 'red'}">Al menos una minúscula</li>
                <li style="color:${v.hasUpper ? 'green' : 'red'}">Al menos una mayúscula</li>
                <li style="color:${v.hasNumber ? 'green' : 'red'}">Al menos un número</li>
                <li style="color:${v.hasSpecial ? 'green' : 'red'}">Al menos un carácter especial (@$!%*#?&._-)</li>
            </ul>
        `;
    }

    function updateConfirm() {
        if (!passwordConfirmInput) return;
        if (passwordConfirmInput.value === "") {
            passwordConfirmInput.classList.remove('border-green-500', 'border-red-500');
            passwordConfirmHelp.textContent = '';
            return;
        }
        if (passwordInput.value === passwordConfirmInput.value) {
            passwordConfirmInput.classList.add('border-green-500');
            passwordConfirmInput.classList.remove('border-red-500');
            passwordConfirmHelp.textContent = 'Las contraseñas coinciden';
            passwordConfirmHelp.style.color = 'green';
        } else {
            passwordConfirmInput.classList.add('border-red-500');
            passwordConfirmInput.classList.remove('border-green-500');
            passwordConfirmHelp.textContent = 'Las contraseñas no coinciden';
            passwordConfirmHelp.style.color = 'red';
        }
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', function (e) {
            updateHelp(e.target.value);
            updateConfirm();
        });
        updateHelp(passwordInput.value || '');
    }
    if (passwordConfirmInput) {
        passwordConfirmInput.addEventListener('input', updateConfirm);
    }
});
