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
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold mb-6 text-center">Connexion</h1>
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['success']); ?></span>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Mot de passe</label>
                    <input type="password" id="password" name="password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Se connecter
                    </button>
                    <a href="register.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Créer un compte
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
