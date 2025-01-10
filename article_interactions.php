<?php
session_start();
require_once 'Article.php';
require_once 'Comment.php';

$article = new Article();
$comment = new Comment();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'like':
                if (isset($_POST['article_id']) && isset($_SESSION['user_id'])) {
                    $result = $article->likeArticle($_POST['article_id'], $_SESSION['user_id']);
                    echo json_encode(['success' => $result]);
                }
                break;
            case 'comment':
                if (isset($_POST['article_id']) && isset($_SESSION['user_id']) && isset($_POST['content'])) {
                    $result = $comment->addComment($_POST['article_id'], $_SESSION['user_id'], $_POST['content']);
                    echo json_encode(['success' => $result]);
                }
                break;
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'download_pdf' && isset($_GET['article_id'])) {
        $pdf_content = $article->generatePDF($_GET['article_id']);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="article.pdf"');
        echo $pdf_content;
        exit;
    }
}