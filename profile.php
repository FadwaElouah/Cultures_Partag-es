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
        if ($user->verifyPassword($userId, $currentPassword)) {
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
        } else {
            $error = "Mot de passe actuel incorrect.";
        }
    }

    // Refresh user info after update
    $userInfo = $user->getUserById($userId);
}
?>
     