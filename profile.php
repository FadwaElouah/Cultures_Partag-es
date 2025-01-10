<?php
session_start();
require_once 'User.php';

$user = new User();

// Check if the user is logged in
if (!$user->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userInfo = $user->getUserById($userId);

$success = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
     // Handle profile picture upload
     $profilePicture = null;
     if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
         $allowed = ['jpg', 'jpeg', 'png', 'gif'];
         $filename = $_FILES['profile_picture']['name'];
         $filetype = pathinfo($filename, PATHINFO_EXTENSION);
         if (in_array(strtolower($filetype), $allowed)) {
             $tempname = $_FILES['profile_picture']['tmp_name'];
             $folder = "uploads/";
             if (!file_exists($folder)) {
                 mkdir($folder, 0777, true);
             }
             $profilePicture = $folder . uniqid() . "." . $filetype;
             if (move_uploaded_file($tempname, $profilePicture)) {
                 // File uploaded successfully
             } else {
                 $error = "Échec du téléchargement de l'image.";
             }
         } else {
             $error = "Type de fichier non autorisé. Veuillez télécharger une image (jpg, jpeg, png, gif).";
         }
     }
     if (empty($error)) {
        
            if (!empty($newPassword)) {
                if ($newPassword === $confirmPassword) {
                    if ($user->updateUser($userId, $name, $email, $profilePicture, $newPassword)) {
                        $success = "Profil mis à jour avec succès.";
                    } else {
                        $error = "Erreur lors de la mise à jour du profil.";
                    }
                } else {
                    $error = "Les nouveaux mots de passe ne correspondent pas.";
                }
            } else {
                if ($user->updateUser($userId, $name, $email, $profilePicture)) {
                    $success = "Profil mis à jour avec succès.";
                } else {
                    $error = "Erreur lors de la mise à jour du profil.";
                }
            }
        
    }

    // Refresh user info after update
    $userInfo = $user->getUserById($userId);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Cultures Partagées</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <header class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white shadow-lg">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div>
                <a href="index.php" class="text-3xl font-semibold hover:text-gray-200 transition duration-300">Cultures Partagées</a>
            </div>
            <div class="flex items-center space-x-4">
                <a href="index.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300">Accueil</a>
                <a href="logout.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300 font-bold">Déconnexion</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">
        <h1 class="text-4xl font-bold text-center text-gray-900 mb-8">Mon Profil</h1>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 flex flex-col my-2">
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="profile_picture">
                        Photo de profil
                    </label>
                    <?php if (!empty($userInfo['profile_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($userInfo['profile_picture']); ?>" alt="Photo de profil" class="w-32 h-32 rounded-full mb-4">
                    <?php endif; ?>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="profile_picture" type="file" name="profile_picture">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Nom
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" name="name" value="<?php echo htmlspecialchars($userInfo['name']); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" value="<?php echo htmlspecialchars($userInfo['email']); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="current_password">
                        Mot de passe actuel
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="current_password" type="password" name="current_password" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="new_password">
                        Nouveau mot de passe (laisser vide pour ne pas changer)
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="new_password" type="password" name="new_password">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">
                        Confirmer le nouveau mot de passe
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="confirm_password" type="password" name="confirm_password">
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Mettre à jour le profil
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white shadow-lg text-white py-4 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2024 Cultures Partagées. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>     