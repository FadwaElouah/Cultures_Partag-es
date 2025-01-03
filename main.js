const form = document.getElementById('form');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const confirmPassword = document.getElementById('confirm_password');

const nameError = document.getElementById('nameError');
const emailError = document.getElementById('emailError');
const passwordError = document.getElementById('passwordError');
const confirmError = document.getElementById('confirmError');

form.addEventListener('submit', function (event) {
    let isValid = true;
    
    // Reset des messages d'erreur
    nameError.textContent = '';
    emailError.textContent = '';
    passwordError.textContent = '';
    confirmError.textContent = '';

    // Vérifier si le champ du nom est vide ou trop court
    if (nameInput.value.trim() === '') {
        nameError.textContent = 'Le nom ne peut pas être vide.';
        isValid = false;
    } else if (nameInput.value.trim().length < 3) {
        nameError.textContent = 'Le nom doit comporter au moins 3 caractères.';
        isValid = false;
    }

    // Vérifier si le champ de l'email est valide
    if (emailInput.value.trim() === '') {
        emailError.textContent = 'L\'email ne peut pas être vide.';
        isValid = false;
    } else {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const emailValid = emailRegex.test(emailInput.value);
        if (!emailValid) {
            emailError.textContent = "Veuillez entrer un email valide.";
            isValid = false;
        }
    }

    // Vérifier si le champ de la mot de passe est valide
    if (passwordInput.value.trim() === '') {
        passwordError.textContent = 'Le mot de passe ne peut pas être vide.';
        isValid = false;
    } else if (passwordInput.value.length < 6) {
        passwordError.textContent = 'Le mot de passe doit comporter au moins 6 caractères.';
        isValid = false;
    }

    // Vérifier si le champ de confirmation du mot de passe est valide
    if (confirmPassword.value.trim() === '') {
        confirmError.textContent = 'Veuillez confirmer votre mot de passe.';
        isValid = false;
    } else if (confirmPassword.value !== passwordInput.value) {
        confirmError.textContent = 'Les mots de passe ne correspondent pas.';
        isValid = false;
    }

    // Empêcher l'envoi du formulaire si des erreurs sont présentes
    if (!isValid) {
        event.preventDefault(); // Empêche le formulaire de se soumettre
    }
});
