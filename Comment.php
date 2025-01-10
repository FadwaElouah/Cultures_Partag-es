<?php
require_once 'config.php';

class Comment {
    private $db;
    private $id_comment;
    private $id_article;
    private $id_utilisateur;
    private $content;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Getter pour id_comment
    public function getIdComment() {
        return $this->id_comment;
    }

    // Setter pour id_comment
    public function setIdComment($id_comment) {
        $this->id_comment = $id_comment;
    }

    // Getter pour id_article
    public function getIdArticle() {
        return $this->id_article;
    }

    // Setter pour id_article
    public function setIdArticle($id_article) {
        $this->id_article = $id_article;
    }

    // Getter pour id_utilisateur
    public function getIdUtilisateur() {
        return $this->id_utilisateur;
    }

    // Setter pour id_utilisateur
    public function setIdUtilisateur($id_utilisateur) {
        $this->id_utilisateur = $id_utilisateur;
    }

    // Getter pour content
    public function getContent() {
        return $this->content;
    }

    // Setter pour content
    public function setContent($content) {
        $this->content = $content;
    }

    // Méthode pour ajouter un commentaire
    public function addComment($article_id, $user_id, $content) {
        $sql = "INSERT INTO comments (id_article, id_utilisateur, content) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$article_id, $user_id, $content]);
    }

    // Méthode pour récupérer les commentaires par article
    public function getCommentsByArticle($article_id) {
        $sql = "SELECT c.*, u.name as user_name FROM comments c 
                JOIN utilisateur u ON c.id_utilisateur = u.id_utilisateur 
                WHERE c.id_article = ? ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$article_id]);
        return $stmt->fetchAll();
    }

    // Méthode pour supprimer un commentaire
    public function deleteComment($comment_id) {
        $sql = "DELETE FROM comments WHERE id_comment = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$comment_id]);
    }
}
