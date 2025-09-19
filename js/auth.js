// js/auth.js - Lógica AVANZADA para Registro e Inicio de Sesión

document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('register-form');

    if (registerForm) {
        const emailInput = document.getElementById('email');
        const emailFeedback = document.getElementById('email-feedback');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirm');
        const passwordConfirmFeedback = document.getElementById('password-confirm-feedback');
        const togglePasswordBtn = document.getElementById('toggle-password');
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('password-strength-text');
        const termsCheckbox = document.getElementById('terms');
        const submitBtn = document.getElementById('register-submit-btn');

        let emailDebounceTimer;
        const validationState = {
            email: false,
            password: false,
            passwordMatch: false,
            terms: false
        };

        // --- VALIDACIÓN GENERAL Y HABILITACIÓN DEL BOTÓN ---
        function checkFormValidity() {
            if (validationState.email && validationState.password && validationState.passwordMatch && validationState.terms) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }

        // --- VALIDACIÓN DE EMAIL (CON DEBOUNCE) ---
        emailInput.addEventListener('input', () => {
            clearTimeout(emailDebounceTimer);
            emailFeedback.textContent = 'Verificando...';
            emailFeedback.className = 'feedback-message';
            validationState.email = false;

            emailDebounceTimer = setTimeout(async () => {
                const email = emailInput.value;
                if (email.length < 5 || !email.includes('@')) {
                    emailFeedback.textContent = 'Por favor, introduce un email válido.';
                    emailFeedback.className = 'feedback-message error';
                    checkFormValidity();
                    return;
                }
                try {
                    const response = await fetch(`${baseURL}api/auth/check_email.php?email=${email}`);
                    const result = await response.json();
                    emailFeedback.textContent = result.message;
                    if (result.available) {
                        emailFeedback.className = 'feedback-message success';
                        validationState.email = true;
                    } else {
                        emailFeedback.className = 'feedback-message error';
                    }
                } catch (error) {
                    emailFeedback.textContent = 'No se pudo verificar el email.';
                    emailFeedback.className = 'feedback-message error';
                }
                checkFormValidity();
            }, 800); // Espera 800ms después de que el usuario deja de teclear
        });

        // --- MOSTRAR/OCULTAR CONTRASEÑA ---
        togglePasswordBtn.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePasswordBtn.innerHTML = type === 'password' ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });

        // --- MEDIDOR DE FORTALEZA DE CONTRASEÑA ---
        passwordInput.addEventListener('input', () => {
            const password = passwordInput.value;
            let score = 0;
            if (password.length > 8) score++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            strengthBar.className = 'strength-bar';
            switch (score) {
                case 1:
                case 2:
                    strengthBar.classList.add('weak');
                    strengthText.textContent = 'Fortaleza: Débil';
                    strengthText.className = 'feedback-message error';
                    validationState.password = false;
                    break;
                case 3:
                    strengthBar.classList.add('medium');
                    strengthText.textContent = 'Fortaleza: Media';
                    strengthText.className = 'feedback-message';
                    validationState.password = true;
                    break;
                case 4:
                    strengthBar.classList.add('strong');
                    strengthText.textContent = 'Fortaleza: Fuerte';
                    strengthText.className = 'feedback-message success';
                    validationState.password = true;
                    break;
                default:
                    strengthText.textContent = '';
                    validationState.password = false;
            }
            validatePasswordMatch();
            checkFormValidity();
        });

        // --- VALIDACIÓN DE COINCIDENCIA DE CONTRASEÑAS ---
        function validatePasswordMatch() {
            if (passwordConfirmInput.value === '' && passwordInput.value === '') {
                passwordConfirmFeedback.textContent = '';
                validationState.passwordMatch = false;
            } else if (passwordConfirmInput.value === passwordInput.value) {
                passwordConfirmFeedback.textContent = 'Las contraseñas coinciden.';
                passwordConfirmFeedback.className = 'feedback-message success';
                validationState.passwordMatch = true;
            } else {
                passwordConfirmFeedback.textContent = 'Las contraseñas no coinciden.';
                passwordConfirmFeedback.className = 'feedback-message error';
                validationState.passwordMatch = false;
            }
            checkFormValidity();
        }
        passwordConfirmInput.addEventListener('input', validatePasswordMatch);

        // --- VALIDACIÓN DE TÉRMINOS ---
        termsCheckbox.addEventListener('change', () => {
            validationState.terms = termsCheckbox.checked;
            checkFormValidity();
        });

        // --- ENVÍO DEL FORMULARIO ---
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!submitBtn.disabled) {
                const formData = new FormData(registerForm);
                const data = Object.fromEntries(formData.entries());

                try {
                    const response = await fetch(`${baseURL}api/auth/register.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });
                    const result = await response.json();
                    if (response.ok) {
                        App.showToast(result.message, 'success');
                        setTimeout(() => { window.location.href = 'login.php'; }, 2000);
                    } else {
                        throw new Error(result.error);
                    }
                } catch (error) {
                    App.showToast(error.message, 'error');
                }
            }
        });
    }

    // En js/auth.js, al final del archivo

    // --- LÓGICA PARA EL FORMULARIO DE LOGIN ---
    const loginForm = document.getElementById('login-form');

    if (loginForm) {
        // Reutilizamos el botón de mostrar/ocultar contraseña si existe
        const passwordInput = document.getElementById('password');
        const togglePasswordBtn = document.getElementById('toggle-password');
        if (passwordInput && togglePasswordBtn) {
            togglePasswordBtn.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                togglePasswordBtn.innerHTML = type === 'password' ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            });
        }

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`${baseURL}api/auth/login.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    App.showToast(result.message, 'success');
                    // Redirigir al panel de usuario después de un login exitoso
                    setTimeout(() => { window.location.href = 'mi_cuenta.php'; }, 1500);
                } else {
                    throw new Error(result.error);
                }
            } catch (error) {
                App.showToast(error.message, 'error');
            }
        });
    }
});