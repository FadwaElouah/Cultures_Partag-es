<?php
require_once 'config.php';

class Category {
    private $db;
    private $id;
    private $name;
    private $description;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getAllCategories() {
        $sql = "SELECT * FROM categories";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function createCategory() {
        $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        // return $stmt->execute([$this->getName(), $this->getDescription()]);
    }

    public function updateCategory() {
        $sql = "UPDATE categories SET name = ?, description = ? WHERE id_categorie = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->getName(), $this->getDescription(), $this->getId()]);
    }

    public function deleteCategory() {
        try {
            $checkQuery = "SELECT COUNT(*) FROM articles WHERE id_categorie = :id";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->execute([':id' => $this->getId()]);
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                $deleteArticlesQuery = "DELETE FROM articles WHERE id_categorie = :id";
                $deleteArticlesStmt = $this->db->prepare($deleteArticlesQuery);
                $deleteArticlesStmt->execute([':id' => $this->getId()]);
            }
            $deleteCategoryQuery = "DELETE FROM categories WHERE id_categorie = :id";
            $deleteCategoryStmt = $this->db->prepare($deleteCategoryQuery);
            return $deleteCategoryStmt->execute([':id' => $this->getId()]);

        } catch(PDOException $e) {
            error_log("Error during deletion: " . $e->getMessage());
            return false;
        }
    }}