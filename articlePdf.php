<?php
session_start();
require_once 'Article.php';
require_once 'User.php';
require_once 'Comment.php';
require_once('tcpdf/tcpdf.php'); // Make sure you have TCPDF library installed

$user = new User();
if (!$user->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$article = new Article();
$comment = new Comment();

$articleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$articleData = $article->getArticleById($articleId);

if (!$articleData) {
    header("Location: index.php");
    exit();
}

// Handle PDF generation and download
if (isset($_GET['download_pdf'])) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Cultures Partagées');
    $pdf->SetTitle($articleData['title']);
    $pdf->SetSubject('Article PDF');

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('dejavusans', '', 12);

    // Add content
    $pdf->WriteHTML('<h1>' . $articleData['title'] . '</h1>', true, false, true, false, '');
    $pdf->WriteHTML('<p>Par ' . $articleData['author_name'] . '</p>', true, false, true, false, '');
    $pdf->WriteHTML('<p>' . $articleData['content'] . '</p>', true, false, true, false, '');

    // Output the PDF as a download
    $pdf->Output($articleData['title'] . '.pdf', 'D');
    exit();
}

// Handle comment submission
if (isset($_POST['submit_comment'])) {
    $comment->addComment($articleId, $_SESSION['user_id'], $_POST['comment_content']);
    header("Location: article.php?id=" . $articleId);
    exit();
}

$comments = $comment->getCommentsByArticle($articleId);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($articleData['title']); ?> - Cultures Partagées</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <header class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white shadow-lg">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div>
                <a href="index.php" class="text-3xl font-semibold hover:text-gray-200 transition duration-300">Cultures Partagées</a>
            </div>
            <div class="flex items-center space-x-4">
                <a href="favorites.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300">Mes Favoris</a>
                <a href="profile.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300">Profil</a>
                <a href="logout.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300 font-bold">Déconnexion</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">
        <article class="bg-white rounded-lg shadow-lg overflow-hidden">
            <img src="<?php echo htmlspecialchars($articleData['image_url']); ?>" alt="Article Image" class="w-full h-64 object-cover">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($articleData['title']); ?></h1>
                <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                    <span>Par <?php echo htmlspecialchars($articleData['author_name']); ?></span>
                    <span><?php echo htmlspecialchars($articleData['category_name']); ?></span>
                </div>
                <div class="prose max-w-none">
                    <?php echo nl2br(htmlspecialchars($articleData['content'])); ?>
                </div>
                <div class="mt-6">
                    <a href="article.php?id=<?php echo $articleId; ?>&download_pdf=1" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i> Télécharger en PDF
                    </a>
                </div>
            </div>
        </article>

        <section class="mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Commentaires</h2>
            <?php foreach ($comments as $com): ?>
                <div class="bg-white p-4 rounded-lg shadow mb-4">
                    <p class="text-gray-800"><?php echo htmlspecialchars($com['content']); ?></p>
                    <p class="text-sm text-gray-500 mt-2">Par <?php echo htmlspecialchars($com['user_name']); ?></p>
                </div>
            <?php endforeach; ?>

            <form action="article.php?id=<?php echo $articleId; ?>" method="POST" class="mt-6">
                <textarea name="comment_content" placeholder="Ajouter un commentaire" class="w-full p-2 border rounded" required></textarea>
                <button type="submit" name="submit_comment" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-300">Commenter</button>
            </form>
        </section>
    </main>

    <footer class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white shadow-lg text-white py-4 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2024 Cultures Partagées. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>

