<?php
session_start();
require_once 'User.php';

$user = new User();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Tous les champs sont obligatoires';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas';
    } else {
        if ($user->register($name, $email, $password)) {
            $_SESSION['success'] = 'Inscription réussie. Vous pouvez maintenant vous connecter.';
            header('Location: login.php');
            exit();
        } else {
            $error = 'Une erreur est survenue lors de l\'inscription';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Cultures Partagées</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-purple-500 to-blue-500 min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-xl w-96 max-w-lg">
        <h1 class="text-3xl font-semibold text-center text-gray-800 mb-8">Inscription</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" id="form">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-semibold mb-2">Nom Complet</label>
                <input type="text" id="name" name="name" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                <span id="nameError" class="text-red-500"></span>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                <input type="email" id="email" name="email" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                <span id="emailError" class="text-red-500"></span>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Mot de passe</label>
                <input type="password" id="password" name="password" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                <span id="passwordError" class="text-red-500"></span>
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 text-sm font-semibold mb-2">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                <span id="confirmError" class="text-red-500"></span>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    S'inscrire
                </button>
                <a href="login.php" class="inline-block font-medium text-sm text-blue-500 hover:text-blue-700">
                    Déjà inscrit ?
                </a>
            </div>
        </form>
    </div>

    <script src="main.js"></script>
</body>
</html>
