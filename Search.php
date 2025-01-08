<?php
require_once 'config.php';

class Search {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function searchArticles($keyword, $author = null, $category = null) {
        $sql = "SELECT a.*, c.name as category_name, u.name as author_name 
                FROM articles a 
                JOIN categories c ON a.id_categorie = c.id_categorie 
                JOIN utilisateur u ON a.id_auteur = u.id_utilisateur 
                WHERE a.status = 'approved' AND (a.title LIKE ? OR a.content LIKE ?)";
        $params = ["%$keyword%", "%$keyword%"];

        if ($author) {
            $sql .= " AND u.name LIKE ?";
            $params[] = "%$author%";
        }

        if ($category) {
            $sql .= " AND c.name LIKE ?";
            $params[] = "%$category%";
        }

        $sql .= " ORDER BY a.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}