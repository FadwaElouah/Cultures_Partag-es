<?php
require_once 'config.php';

class Comment {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addComment($article_id, $user_id, $content) {
        $sql = "INSERT INTO comments (id_article, id_utilisateur, content) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$article_id, $user_id, $content]);
    }

    public function getCommentsByArticle($article_id) {
        $sql = "SELECT c.*, u.name as user_name FROM comments c 
                JOIN utilisateur u ON c.id_utilisateur = u.id_utilisateur 
                WHERE c.id_article = ? ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$article_id]);
        return $stmt->fetchAll();
    }

    public function deleteComment($comment_id) {
        $sql = "DELETE FROM comments WHERE id_comment = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$comment_id]);
    }
}