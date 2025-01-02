<?php
require_once 'config.php';

class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function getAllCategories() {
        $sql = "SELECT * FROM categories";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    public function createCategory($name, $description) {
        $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $description]);
    }
}