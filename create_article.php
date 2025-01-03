<?php
session_start();
require_once 'Article.php';
require_once 'Category.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'auteur' && $_SESSION['user_role'] !== 'admin')) {
    header('Location: index.php');
    exit();
}

$article = new Article();
$category = new Category();
$categories = $category->getAllCategories();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category'];

    if (empty($title) || empty($content) || empty($category_id)) {
        $error = 'Tous les champs sont obligatoires';
    } else {
        if ($article->createArticle($title, $content, $category_id, $_SESSION['user_id'])) {
            $success = 'Article créé avec succès. Il sera publié après validation par un administrateur.';
        } else {
            $error = 'Une erreur est survenue lors de la création de l\'article';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un article - Cultures Partagées</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white shadow-lg">
        <nav class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <a href="index.php" class="text-3xl font-semibold hover:text-gray-200 transition duration-300">Cultures Partagées</a>
                </div>
                <div>
                    <a href="index.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300">Retour à l'accueil</a>
                    <a href="logout.php" class="hover:bg-white hover:text-gray-800 px-4 py-2 rounded-md transition duration-300 font-bold">Déconnexion</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold mb-6">Créer un article</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <form action="create_article.php" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                    Titre
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="title" type="text" name="title" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="category">
                    Catégorie
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category" name="category" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id_categorie']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="content">
                    Contenu
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="content" name="content" rows="10" required></textarea>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Créer l'article
                </button>
            </div>
        </form>
    </main>

    <footer class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white shadow-lg text-white py-4 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2024 Cultures Partagées. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>

