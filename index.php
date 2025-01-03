<?php
session_start();
require_once 'Article.php';
require_once 'Category.php';

$article = new Article();
$category = new Category();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : null;

$articles = $article->getAllArticles($page, 10, $selectedCategory);
$categories = $category->getAllCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cultures Partagées</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow">
        <nav class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <a href="index.php" class="text-gray-800 text-xl font-bold md:text-2xl">Cultures Partagées</a>
                </div>
                <div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <a href="admin_dashboard.php" class="text-gray-800 hover:text-gray-600 mr-4">Tableau de bord Admin</a>
                        <?php endif; ?>
                        <?php if ($_SESSION['user_role'] === 'auteur' || $_SESSION['user_role'] === 'admin'): ?>
                            <a href="create_article.php" class="text-gray-800 hover:text-gray-600 mr-4">Créer un article</a>
                        <?php endif; ?>
                        <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Déconnexion</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-800 hover:text-gray-600 mr-4">Connexion</a>
                        <a href="register.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Inscription</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold mb-6">Derniers Articles</h1>

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Catégories</h2>
            <div class="flex flex-wrap gap-2">
                <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded">Toutes</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="index.php?category=<?php echo $cat['id_categorie']; ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded"><?php echo htmlspecialchars($cat['name']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($articles as $art): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($art['title']); ?></h2>
                        <p class="text-gray-600 mb-4"><?php echo substr(htmlspecialchars($art['content']), 0, 150) . '...'; ?></p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Par <?php echo htmlspecialchars($art['author_name']); ?></span>
                            <span><?php echo htmlspecialchars($art['category_name']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            <?php
            $totalArticles = count($article->getAllArticles(1, PHP_INT_MAX, $selectedCategory));
            $totalPages = ceil($totalArticles / 10);
            for ($i = 1; $i <= $totalPages; $i++):
            ?>
                <a href="index.php?page=<?php echo $i; ?><?php echo $selectedCategory ? '&category=' . $selectedCategory : ''; ?>" class="mx-1 px-3 py-2 bg-gray-200 text-gray-700 rounded-md <?php echo $i === $page ? 'bg-blue-500 text-white' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 mt-[12rem]">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2024 Cultures Partagées. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
