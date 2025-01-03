<?php
session_start();
require_once 'User.php';

$user = new User();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $loggedInUser = $user->login($email, $password);
    if ($loggedInUser) {
        $_SESSION['user_id'] = $loggedInUser['id_utilisateur'];
        $_SESSION['user_name'] = $loggedInUser['name'];
        $_SESSION['user_role'] = $loggedInUser['role'];
        header('Location: index.php');
        exit();
    } else {
        $error = 'Email ou mot de passe incorrect';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Cultures Partagées</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-600 to-pink-500 min-h-screen flex items-center justify-center">

    <div class="bg-white p-10 rounded-3xl shadow-lg w-full max-w-sm">
        <h1 class="text-4xl font-semibold text-center text-gray-800 mb-6">Connexion</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
                <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-5">
                <label for="email" class="block text-lg font-medium text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email"  class="shadow-lg border rounded-lg w-full py-3 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition duration-300">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-lg font-medium text-gray-700 mb-2">Mot de passe</label>
                <input type="password" id="password" name="password"  class="shadow-lg border rounded-lg w-full py-3 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition duration-300">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    Se connecter
                </button>
                <a href="register.php" class="text-indigo-600 hover:text-indigo-700 font-medium">Créer un compte</a>
            </div>
        </form>
    </div>

</body>
</html>
