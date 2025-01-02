<?php
require_once 'config.php';

function getUserById($id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getArticles($page = 1, $limit = 10, $category_id = null) {
    $db = Database::getInstance()->getConnection();
    $offset = ($page - 1) * $limit;
    $sql = "SELECT a.*, u.name as author_name, c.name as category_name 
            FROM articles a 
            JOIN users u ON a.user_id = u.id 
            JOIN categories c ON a.category_id = c.id 
            WHERE a.status = 'approved'";
    
    if ($category_id) {
        $sql .= " AND a.category_id = :category_id";
    }
    
    $sql .= " ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
    if ($category_id) {
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalArticles($category_id = null) {
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT COUNT(*) FROM articles WHERE status = 'approved'";
    if ($category_id) {
        $sql .= " AND category_id = :category_id";
    }
    $stmt = $db->prepare($sql);
    if ($category_id) {
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getCategories() {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM categories");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createArticle($title, $content, $user_id, $category_id) {
    $db = Database::getInstance()->getConnection();
    $sql = "INSERT INTO articles (title, content, user_id, category_id) VALUES (:title, :content, :user_id, :category_id)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':category_id', $category_id);
    return $stmt->execute();
}

function updateArticle($id, $title, $content, $category_id) {
    $db = Database::getInstance()->getConnection();
    $sql = "UPDATE articles SET title = :title, content = :content, category_id = :category_id WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':category_id', $category_id);
    return $stmt->execute();
}

function deleteArticle($id) {
    $db = Database::getInstance()->getConnection();
    $sql = "DELETE FROM articles WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

function getPendingArticles() {
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT a.*, u.name as author_name, c.name as category_name 
            FROM articles a 
            JOIN users u ON a.user_id = u.id 
            JOIN categories c ON a.category_id = c.id 
            WHERE a.status = 'pending'";
    $stmt = $db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function approveArticle($id) {
    $db = Database::getInstance()->getConnection();
    $sql = "UPDATE articles SET status = 'approved' WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

function rejectArticle($id) {
    $db = Database::getInstance()->getConnection();
    $sql = "UPDATE articles SET status = 'rejected' WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

function createCategory($name) {
    $db = Database::getInstance()->getConnection();
    $sql = "INSERT INTO categories (name) VALUES (:name)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':name', $name);
    return $stmt->execute();
}

function updateCategory($id, $name) {
    $db = Database::getInstance()->getConnection();
    $sql = "UPDATE categories SET name = :name WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

function deleteCategory($id) {
    $db = Database::getInstance()->getConnection();
    $sql = "DELETE FROM categories WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

