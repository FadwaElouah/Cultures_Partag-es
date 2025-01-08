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
    public function updateCategory($id, $name, $description) {
        $sql = "UPDATE categories SET name = ?, description = ? WHERE id_categorie = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $description, $id]);
    }

   

    // public function deleteCategory($id) {
    //     $sql = "DELETE FROM categories WHERE id_categorie = ?";
    //     $stmt = $this->db->prepare($sql);
    //     return $stmt->execute([$id]);
    // }



    public function deleteCategory($id) {
        try {
            // Vérifier d'abord s'il y a des articles dans cette catégorie
            $checkQuery = "SELECT COUNT(*) FROM articles WHERE id_categorie = :id";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->execute([':id' => $id]);
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                // Si on a des articles, on les supprime d'abord
                $deleteArticlesQuery = "DELETE FROM articles WHERE id_categorie = :id";
                $deleteArticlesStmt = $this->db->prepare($deleteArticlesQuery);
                $deleteArticlesStmt->execute([':id' => $id]);
            }

            // Ensuite on supprime la catégorie
            $deleteCategoryQuery = "DELETE FROM categories WHERE id_categorie = :id";
            $deleteCategoryStmt = $this->db->prepare($deleteCategoryQuery);
            return $deleteCategoryStmt->execute([':id' => $id]);

        } catch(PDOException $e) {
            error_log("Erreur lors de la suppression : " . $e->getMessage());
            return false;
        }
    }

    // ... autres méthodes de la classe
}
