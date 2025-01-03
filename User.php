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

    public function updateUser($id, $name, $email, $role) {
        $sql = "UPDATE utilisateur SET name = ?, email = ?, role = ? WHERE id_utilisateur = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $email, $role, $id]);
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM utilisateur WHERE id_utilisateur = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}