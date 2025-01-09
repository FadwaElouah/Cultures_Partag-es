<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image_url = $_POST['image_url'];
    $id_article = $_POST['id_article'];

    // Requête SQL pour mettre à jour l'article
    $query = "UPDATE articles SET title = :title, content = :content, image_url = :image_url WHERE id_article = :id_article";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'title' => $title,
        'content' => $content,
        'image_url' => $image_url,
        'id_article' => $id_article
    ]);

    echo "Article mis à jour avec succès!";
    // Redirection vers la page d'accueil après mise à jour
    header("Location: index.php");
}
?>
