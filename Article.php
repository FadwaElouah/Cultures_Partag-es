<?php
require_once 'config.php';

class Article {
    private $db;
    private $id;
    private $title;
    private $content;
    private $category_id;
    private $author_id;
    private $status;
    private $image_url;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getContent() {
        return $this->content;
    }

    public function getCategoryId() {
        return $this->category_id;
    }

    public function getAuthorId() {
        return $this->author_id;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getImageUrl() {
        return $this->image_url;
    }

   
    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function setCategoryId($category_id) {
        $this->category_id = $category_id;
    }

    public function setAuthorId($author_id) {
        $this->author_id = $author_id;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
    public function setImageUrl($image_url) {
        $this->image_url = $image_url;
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

    public function createArticle() {
        if (empty($this->getTitle()) || empty($this->getContent()) || empty($this->getCategoryId()) || empty($this->getAuthorId())) {
            throw new InvalidArgumentException("All required fields must be filled.");
        }

        $sql = "INSERT INTO articles (title, content, id_categorie, id_auteur, image_url, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $this->db->prepare($sql);
    
        try {
            $result = $stmt->execute([
                $this->getTitle(),
                $this->getContent(),
                $this->getCategoryId(),
                $this->getAuthorId(),
                $this->getImageUrl()
            ]);
            if (!$result) {
                throw new PDOException("Failed to insert the article.");
            }
            return true;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new Exception("An error occurred while creating the article. Please try again later.");
        }
    }
    public function updateArticle() {
        $sql = "UPDATE articles SET title = ?, content = ?, id_categorie = ? WHERE id_article = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->getTitle(), $this->getContent(), $this->getCategoryId(), $this->getId()]);
    }

    public function deleteArticle() {
        $sql = "DELETE FROM articles WHERE id_article = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->getId()]);
    }

    public function toggleFavorite($userId) {
        $checkSql = "SELECT * FROM favorites WHERE id_article = ? AND id_utilisateur = ?";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([$this->getId(), $userId]);
        
        if ($checkStmt->rowCount() == 0) {
            $sql = "INSERT INTO favorites (id_article, id_utilisateur) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$this->getId(), $userId]);
        } else {
            $sql = "DELETE FROM favorites WHERE id_article = ? AND id_utilisateur = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$this->getId(), $userId]);
        }
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

    public function approveArticle() {
        $this->setStatus('approved');
        $sql = "UPDATE articles SET status = ? WHERE id_article = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->getStatus(), $this->getId()]);
    }

    public function rejectArticle() {
        $this->setStatus('rejected');
        $sql = "UPDATE articles SET status = ? WHERE id_article = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->getStatus(), $this->getId()]);
    }

    public function getArticleById($id) {
        $sql = "SELECT * FROM articles WHERE id_article = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $article = $stmt->fetch();
        if ($article) {
            $this->setId($article['id_article']);
            $this->setTitle($article['title']);
            $this->setContent($article['content']);
            $this->setCategoryId($article['id_categorie']);
            $this->setAuthorId($article['id_auteur']);
            $this->setStatus($article['status']);
        }
        return $article;
    }

    public function addToFavorites($user_id) {
        $sql = "INSERT INTO favorites (id_article, id_utilisateur) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->getId(), $user_id]);
    }

    public function generatePDF() {
        require_once('tcpdf/tcpdf.php');

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle($this->getTitle());
        $pdf->SetHeaderData('', 0, $this->getTitle(), '');
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->AddPage();
        $pdf->writeHTML($this->getContent(), true, false, true, false, '');

        return $pdf->Output($this->getTitle() . '.pdf', 'S');
    }

    public function addTag($tag_name) {
        $sql = "INSERT INTO tags (name) VALUES (?) ON DUPLICATE KEY UPDATE id_tag = LAST_INSERT_ID(id_tag)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tag_name]);
        $tag_id = $this->db->lastInsertId();

        $sql = "INSERT INTO article_tags (id_article, id_tag) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->getId(), $tag_id]);
    }

    public function getFavoriteArticles($userId) {
        $sql = "SELECT a.*, c.name as category_name, u.name as author_name 
                FROM articles a
                JOIN favorites f ON a.id_article = f.id_article
                JOIN categories c ON a.id_categorie = c.id_categorie
                JOIN utilisateur u ON a.id_auteur = u.id_utilisateur
                WHERE f.id_utilisateur = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function likeArticle($userId) {
        $checkSql = "SELECT * FROM likes WHERE id_article = ? AND id_utilisateur = ?";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([$this->getId(), $userId]);
        
        if ($checkStmt->rowCount() == 0) {
            $sqlLike = "INSERT INTO likes (id_article, id_utilisateur) VALUES (?, ?)";
            $stmtLike = $this->db->prepare($sqlLike);
            $resultLike = $stmtLike->execute([$this->getId(), $userId]);

            $checkFavSql = "SELECT * FROM favorites WHERE id_article = ? AND id_utilisateur = ?";
            $checkFavStmt = $this->db->prepare($checkFavSql);
            $checkFavStmt->execute([$this->getId(), $userId]);
            
            if ($checkFavStmt->rowCount() == 0) {
                $sqlFavorite = "INSERT INTO favorites (id_article, id_utilisateur) VALUES (?, ?)";
                $stmtFavorite = $this->db->prepare($sqlFavorite);
                $resultFavorite = $stmtFavorite->execute([$this->getId(), $userId]);
            } else {
                $resultFavorite = true;
            }

            return $resultLike && $resultFavorite;
        }
        
        return false;
    }
    public function uploadImage($file) {
        $target_dir = "uploads/";
        $target_file = $target_dir . uniqid() . '_' . basename($file["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($file["tmp_name"]);
        if($check === false) {
            return "File is not an image.";
        }

        // Check file size
        if ($file["size"] > 5000000) { // Increased to 5MB
            return "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            return "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // if everything is ok, try to upload file
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $this->setImageUrl($target_file);
            return true;
        } else {
            return "Sorry, there was an error uploading your file.";
        }
    }
}

