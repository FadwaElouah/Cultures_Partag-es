<?php
session_start();
require_once 'Article.php';
require_once 'User.php';

// Check if the user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$article = new Article();

// Get user's favorite articles
$favoriteArticles = $article->getFavoriteArticles($_SESSION['user_id']);

// Handle removing from favorites
if (isset($_POST['remove_favorite'])) {
    $article->toggleFavorite($_POST['article_id'], $_SESSION['user_id']);
    // Refresh the page to update the list
    header("Location: favorites.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris - Cultures Partagées</title>
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
                <a href="index.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300">Accueil</a>
                <?php if ($user->isAdmin()): ?>
                    <a href="admin_dashboard.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300">Admin</a>
                <?php endif; ?>
                <?php if ($user->isAuthor() || $user->isAdmin()): ?>
                    <a href="create_article.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300">Créer un article</a>
                <?php endif; ?>
                <a href="profile.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300">Profil</a>
                <a href="logout.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300 font-bold">Déconnexion</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">
        <h1 class="text-4xl font-bold text-center text-gray-900 mb-8">Mes Articles Favoris</h1>

        <?php if (empty($favoriteArticles)): ?>
            <p class="text-center text-gray-600">Vous n'avez pas encore d'articles favoris.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($favoriteArticles as $favArt): ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                        <img src="<?php echo htmlspecialchars($favArt['image_url']); ?>" alt="Image de l'article" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($favArt['title']); ?></h2>
                            <p class="text-gray-600 mb-4"><?php echo substr(htmlspecialchars($favArt['content']), 0, 150) . '...'; ?></p>
                            <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                                <span>Par <?php echo htmlspecialchars($favArt['author_name']); ?></span>
                                <span><?php echo htmlspecialchars($favArt['category_name']); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <a href="article.php?id=<?php echo $favArt['id_article']; ?>" class="text-blue-500 hover:text-blue-600 transition duration-300">Lire l'article</a>
                                <form action="favorites.php" method="POST">
                                    <input type="hidden" name="article_id" value="<?php echo $favArt['id_article']; ?>">
                                    <button type="submit" name="remove_favorite" class="text-red-500 hover:text-red-600 transition duration-300">
                                        <i class="fas fa-heart-broken"></i> Retirer des favoris
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white shadow-lg text-white py-4 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2024 Cultures Partagées. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>

