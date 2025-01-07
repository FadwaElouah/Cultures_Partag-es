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

    public function createArticle($title, $content, $category_id, $author_id) {
        $sql = "INSERT INTO articles (title, content, id_categorie, id_auteur) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $content, $category_id, $author_id]);
    }

    public function updateArticle($id, $title, $content, $category_id) {
        $sql = "UPDATE articles SET title = ?, content = ?, id_categorie = ? WHERE id_article = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $content, $category_id, $id]);
    }

    public function deleteArticle($id) {
        $sql = "DELETE FROM articles WHERE id_article = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getPendingArticles() {
        $sql = "SELECT a.*, c.name as category_name, u.name as author_name 
                FROM articles a 
                JOIN categories c ON a.id_categorie = c.id_categorie 
                JOIN utilisateur u ON a.id_auteur = u.id_utilisateur 
                WHERE a.status = 'pending'";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function approveArticle($id) { 
        $sql = "UPDATE articles SET status = 'approved' WHERE id_article = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function rejectArticle($id) {
        $sql = "UPDATE articles SET status = 'rejected' WHERE id_article = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    // ===
    // Cette fonction pour récupérer un article par son ID
public function getArticleById($id) {
    $sql = "SELECT * FROM articles WHERE id_article = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}
public function likeArticle($article_id, $user_id) {
    $sql = "INSERT INTO likes (id_article, id_utilisateur) VALUES (?, ?)";
    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute([$article_id, $user_id]);

    if ($result) {
        $this->addToFavorites($article_id, $user_id);
    }

    return $result;
}
public function addToFavorites($article_id, $user_id) {
    $sql = "INSERT INTO favorites (id_article, id_utilisateur) VALUES (?, ?)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$article_id, $user_id]);
}

}
