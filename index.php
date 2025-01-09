<?php
session_start();
require_once 'Article.php';
require_once 'Category.php';
require_once 'User.php';
require_once 'Comment.php';
require_once 'Search.php';

// Check if the user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$article = new Article();
$category = new Category();
$comment = new Comment();
$search = new Search();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Handle search    
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$searchAuthor = isset($_GET['author']) ? $_GET['author'] : '';
$searchCategory = isset($_GET['search_category']) ? $_GET['search_category'] : '';

if (!empty($searchKeyword) || !empty($searchAuthor) || !empty($searchCategory)) {
    $articles = $search->searchArticles($searchKeyword, $searchAuthor, $searchCategory);
} else {
    $articles = $article->getAllArticles($page, 10, $selectedCategory);
}

$categories = $category->getAllCategories();

// Handle like/favorite
if (isset($_POST['like_article'])) {
    $article->likeArticle($_POST['article_id'], $_SESSION['user_id']);
}

// Handle comment submission
if (isset($_POST['submit_comment'])) {
    $comment->addComment($_POST['article_id'], $_SESSION['user_id'], $_POST['comment_content']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cultures Partagées</title>
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
                <?php if ($user->isLoggedIn()): ?>
                    <?php if ($user->isAdmin()): ?>
                        <a href="admin_dashboard.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300">Admin</a>
                    <?php endif; ?>
                    <?php if ($user->isAuthor() || $user->isAdmin()): ?>
                        <a href="create_article.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300">Créer un article</a>
                    <?php endif; ?>
                    <a href="profile.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300">Profil</a>
                    <a href="logout.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300 font-bold">Déconnexion</a>
                <?php else: ?>
                    <a href="login.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300">Connexion</a>
                    <a href="register.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition duration-300">Inscription</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">
        <h1 class="text-4xl font-bold text-center text-gray-900 mb-8">Derniers Articles</h1>

        <!-- Search form -->
        <form action="index.php" method="GET" class="mb-8">
            <div class="flex flex-wrap gap-4 justify-center">
                <input type="text" name="search" placeholder="Rechercher un article" class="px-9 py-2 border rounded-lg" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                <!-- <input type="text" name="author" placeholder="Auteur" class="px-4 py-2 border rounded-lg" value="<?php echo htmlspecialchars($searchAuthor); ?>"> -->
                <!-- <select name="search_category" class="px-4 py-2 border rounded-lg">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id_categorie']; ?>" <?php echo $searchCategory == $cat['id_categorie'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select> -->
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">Rechercher</button>
            </div>
        </form>

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2 text-gray-800">Catégories</h2>
            <div class="flex flex-wrap gap-2 justify-center">
                <a href="index.php" class="bg-indigo-200 hover:bg-indigo-300 text-indigo-800 font-semibold py-2 px-4 rounded-lg transition duration-300">Toutes</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="index.php?category=<?php echo $cat['id_categorie']; ?>" class="bg-indigo-200 hover:bg-indigo-300 text-indigo-800 font-semibold py-2 px-4 rounded-lg transition duration-300"><?php echo htmlspecialchars($cat['name']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($articles as $art): ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                     <!-- Affichage de l'image dynamique -->
                    <img src="<?php echo htmlspecialchars($art['image_url']); ?>" alt="Article Image" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($art['title']); ?></h2>
                        <p class="text-gray-600 mb-4"><?php echo substr(htmlspecialchars($art['content']), 0, 150) . '...'; ?></p>
                        <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                            <span>Par <?php echo htmlspecialchars($art['author_name']); ?></span>
                            <span><?php echo htmlspecialchars($art['category_name']); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <form action="index.php" method="POST">
                                <input type="hidden" name="article_id" value="<?php echo $art['id_article']; ?>">
                                <button type="submit" name="like_article" class="text-red-500 hover:text-red-600 transition duration-300">
                                    <i class="fas fa-heart"></i> J'aime
                                </button>
                            </form>
                            <a href="article.php?id=<?php echo $art['id_article']; ?>" class="text-blue-500 hover:text-blue-600 transition duration-300">Lire plus</a>
                        </div>
                    </div>
                </div>  
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center space-x-2">
            <?php
            $totalArticles = count($article->getAllArticles(1, PHP_INT_MAX, $selectedCategory));
            $totalPages = ceil($totalArticles / 10);
            for ($i = 1; $i <= $totalPages; $i++):
            ?>
                <a href="index.php?page=<?php echo $i; ?><?php echo $selectedCategory ? '&category=' . $selectedCategory : ''; ?>" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition duration-300 <?php echo $i === $page ? 'bg-indigo-700' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>


   
 
    

    <footer class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white shadow-lg text-white py-4 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2024 Cultures Partagées. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>