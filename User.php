<?php
require_once 'config.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public function isAuthor() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'auteur';
    }

    public function register($name, $email, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO utilisateur (name, email, password, role) VALUES (?, ?, ?, 'utilisateur')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $email, $hashed_password]);
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM utilisateur WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getAllUsers() {
        $sql = "SELECT * FROM utilisateur";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function updateUser($id, $name, $email) {
        $sql = "UPDATE utilisateur SET name = ?, email = ? WHERE id_utilisateur = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $email,  $id]);
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM utilisateur WHERE id_utilisateur = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
     //new
     public function updateProfile($id, $name, $email, $profile_picture = null) {
        $sql = "UPDATE utilisateur SET name = ?, email = ?";
        $params = [$name, $email];

        if ($profile_picture) {
            $sql .= ", profile_picture = ?";
            $params[] = $profile_picture;
        }
        

        $sql .= " WHERE id_utilisateur = ?";
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    // public function softDeleteUser($id) {
    //     $sql = "UPDATE utilisateur SET is_active = 0 WHERE id_utilisateur = ?";
    //     $stmt = $this->db->prepare($sql);
    //     return $stmt->execute([$id]);
    // }

    public function softDeleteUser($userId) {
        try {
            // Au lieu de supprimer l'utilisateur, on le désactive simplement
            $query = "UPDATE utilisateur SET is_active = 0 WHERE id_utilisateur = :id";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':id' => $userId]);
        } catch(PDOException $e) {
            error_log("Erreur lors de la désactivation : " . $e->getMessage());
            return false;
        }
    }

    // Si tu veux vraiment supprimer l'utilisateur définitivement
    public function hardDeleteUser($userId) {
        try {
            $query = "DELETE FROM utilisateur WHERE id_utilisateur = :id";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':id' => $userId]);
        } catch(PDOException $e) {
            error_log("Erreur lors de la suppression : " . $e->getMessage());
            return false;
        }
    }

    // Pour récupérer uniquement les utilisateurs actifs
    public function getAllActiveUsers() {
        try {
            $query = "SELECT * FROM utilisateur WHERE is_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erreur lors de la récupération : " . $e->getMessage());
            return [];
        }
    }
    

    public function sendWelcomeEmail($email, $name, $role) {
        $subject = "Bienvenue sur Cultures Partagées";
        
        if ($role === 'auteur') {
            $message = "Bonjour $name,\n\nBienvenue sur Cultures Partagées! Nous sommes ravis de vous avoir parmi nos auteurs. N'hésitez pas à commencer à publier vos articles dès maintenant.";
        } else {
            $message = "Bonjour $name,\n\nBienvenue sur Cultures Partagées! Nous vous invitons à explorer notre plateforme, commenter les articles et ajouter vos favoris.";
        }

        // Use PHP's mail function or a library like PHPMailer to send the email
        return mail($email, $subject, $message);
    }

    public function getUserById($userId) {
        $sql = "SELECT * FROM utilisateur WHERE id_utilisateur = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}


