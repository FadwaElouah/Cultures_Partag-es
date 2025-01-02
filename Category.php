<?php
require_once 'config.php';


class Article {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllArticles($page = 1, $perPage = 10, $category = null) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT a.*, c.name as category_name, u.name as author_name 
                FROM articles a 
                JOIN categories c ON a.id_categorie = c.id_categorie 
                JOIN utilisateur u ON a.id_auteur = u.id_utilisateur 
                WHERE a.status = 'approved'";
        
        if ($category) {
            $sql .= " AND a.id_categorie = ?";
        }
        
        $sql .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        
        if ($category) {
            $stmt->execute([$category, $perPage, $offset]);
        } else {
            $stmt->execute([$perPage, $offset]);
        }
        
        return $stmt->fetchAll();
    }
 
}
