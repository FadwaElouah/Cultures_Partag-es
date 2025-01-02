<?php
session_start();
require_once 'User.php';
require_once 'Category.php';
require_once 'Article.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$user = new User();
$category = new Category();
$article = new Article();

$users = $user->getAllUsers();
$categories = $category->getAllCategories();
$pendingArticles = $article->getPendingArticles();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve_article'])) {
        if ($article->approveArticle($_POST['article_id'])) {
            $success = 'Article approuvé avec succès';
        } else {
            $error = 'Erreur lors de l\'approbation de l\'article';
        }
    } elseif (isset($_POST['reject_article'])) {
        if ($article->rejectArticle($_POST['article_id'])) {
            $success = 'Article rejeté avec succès';
        } else {
            $error = 'Erreur lors du rejet de l\'article';
        }
    } elseif (isset($_POST['create_category'])) {
        $categoryName = $_POST['category_name'];
        $categoryDescription = $_POST['category_description'];
        if ($category->createCategory($categoryName, $categoryDescription)) {
            $success = 'Catégorie créée avec succès';
            $categories = $category->getAllCategories();
        } else {
            $error = 'Erreur lors de la création de la catégorie';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin - Cultures Partagées</title>
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
                    <a href="index.php" class="text-gray-800 hover:text-gray-600 mr-4">Retour à l'accueil</a>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Déconnexion</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold mb-6">Tableau de bord Administrateur</h1>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-2xl font-bold mb-4">Gestion des utilisateurs</h2>
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-left">Nom</th>
                                <th class="text-left">Email</th>
                                <th class="text-left">Rôle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td><?php echo htmlspecialchars($u['role']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold mb-4">Gestion des catégories</h2>
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <form action="admin_dashboard.php" method="POST" class="mb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="category_name">
                                Nom de la catégorie
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category_name" type="text" name="category_name" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="category_description">
                                Description
                            </label>
                            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category_description" name="category_description" rows="3" required></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" name="create_category">
                                Créer la catégorie
                            </button>
                        </div>
                    </form>
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-left">Nom</th>
                                <th class="text-left">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td><?php echo htmlspecialchars($cat['description']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">Articles en attente d'approbation</h2>
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <?php if (empty($pendingArticles)): ?>
                    <p>Aucun article en attente d'approbation.</p>
                <?php else: ?>
                    <?php foreach ($pendingArticles as $article): ?>
                        <div class="mb-4 p-4 border rounded">
                            <h3 class="text-xl font-bold"><?php echo htmlspecialchars($article['title']); ?></h3>
                            <p class="mb-2">Par <?php echo htmlspecialchars($article['author_name']); ?> dans <?php echo htmlspecialchars($article['category_name']); ?></p>
                            <p class="mb-4"><?php echo substr(htmlspecialchars($article['content']), 0, 200) . '...'; ?></p>
                            <form action="admin_dashboard.php" method="POST" class="inline-block">
                                <input type="hidden" name="article_id" value="<?php echo $article['id_article']; ?>">
                                <button type="submit" name="approve_article" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">Approuver</button>
                                <button type="submit" name="reject_article" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Rejeter</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2023 Cultures Partagées. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>

