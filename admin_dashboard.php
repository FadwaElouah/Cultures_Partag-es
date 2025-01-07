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
// -------------------------------------------------
    // $orderedUser = [];
    // foreach($users as $user){
    //     $orderedUser[] = [
    //         'nom utilisateur' => $user['name'];
    //     ]

    // }
// -------------------------------------------------



$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve_article'])) {
        if ($article->approveArticle($_POST['article_id'])) {
            $success = 'Article approuvé avec succès';
        } else {
            $error = 'Erreur lors de l\'approbation de l\'article';
        }
    } 
    elseif (isset($_POST['reject_article'])) {
        if ($article->rejectArticle($_POST['article_id'])) {
            $success = 'Article rejeté avec succès';
        } else {
            $error = 'Erreur lors du rejet de l\'article';
        }
    }
     elseif (isset($_POST['create_category'])) {
        $categoryName = $_POST['category_name'];
        $categoryDescription = $_POST['category_description'];
        if ($category->createCategory($categoryName, $categoryDescription)) {
            $success = 'Catégorie créée avec succès';
            $categories = $category->getAllCategories();
        } else {
            $error = 'Erreur lors de la création de la catégorie';
        }
    }
      // update user
      elseif (isset($_POST['update_user'])) {
        $userId = $_POST['user_id'];
        $name = $_POST['user_name'];
        $email = $_POST['user_email'];
        $role = $_POST['user_role'];
        if ($user->updateUser($userId, $name, $email, $role)) {
            $success = 'Utilisateur mis à jour avec succès';
            $users = $user->getAllUsers();
        } else {
            $error = 'Erreur lors de la mise à jour de l\'utilisateur';
        }
    }
      // delete user
      elseif (isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];
        if ($user->softDeleteUser($userId)) {
            $success = 'Utilisateur supprimé avec succès';
            $users = $user->getAllUsers();
        } else {
            $error = 'Erreur lors de la suppression de l\'utilisateur';
        }
    }
    //  update category
    elseif (isset($_POST['update_category'])) {
        $categoryId = $_POST['category_id'];
        $name = $_POST['category_name'];
        $description = $_POST['category_description'];
        if ($category->updateCategory($categoryId, $name, $description)) {
            $success = 'Catégorie mise à jour avec succès';
            $categories = $category->getAllCategories();
        } else {
            $error = 'Erreur lors de la mise à jour de la catégorie';
        }
    } 
   //  delete category
    elseif (isset($_POST['delete_category'])) {
        $categoryId = $_POST['category_id'];
        if ($category->deleteCategory($categoryId)) {
            $success = 'Catégorie supprimée avec succès';
            $categories = $category->getAllCategories();
        } else {
            $error = 'Erreur lors de la suppression de la catégorie';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                <h2 class="text-2xl text-green-400 font-bold mb-4">Gestion des utilisateurs</h2>
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <table class="w-full">
       <table class="w-full border-collapse border border-gray-300">
          <thead class="bg-gray-300">
        <tr>
            <th class="text-left border border-gray-300 px-4 py-2">Nom</th>
            <th class="text-left border border-gray-300 px-4 py-2">Email</th>
            <th class="text-left border border-gray-300 px-4 py-2">Rôle</th>
            <th class="text-left border border-gray-300 px-4 py-2">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php $loopIndex = 0;?>
        <!-- <?php foreach ($users as $u): ?>
            <tr class="<?php echo $loopIndex % 2 === 0 ? 'bg-white' : 'bg-gray-200'; ?>">
                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($u['name']); ?></td>
                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($u['email']); ?></td>
                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($u['role']); ?></td>
            </tr>
     <?php $loopIndex++; endforeach; ?>
      -->
      <?php foreach ($users as $u): ?>
              <tr>
                <td><?php echo htmlspecialchars($u['name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['role']); ?></td>
                <td>
                     <button onclick="openUserModal(<?php echo htmlspecialchars(json_encode($u)); ?>)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                      <form action="admin_dashboard.php" method="POST" class="inline">
                        <input type="hidden" name="user_id" value="<?php echo $u['id_utilisateur']; ?>">
                        <button type="submit" name="delete_user" class="text-red-500 hover:text-red-700" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');"><i class="fas fa-trash"></i></button>
                      </form>
                 </td>
             </tr>
     <?php endforeach; ?>
    </tbody>
</table>

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
 <table class="w-full border border-gray-300">
    <thead class="bg-gray-300">
        <tr>
            <th class="text-left px-4 py-2 border-b">Nom</th>
            <th class="text-left px-4 py-2 border-b">Description</th>
            <th class="text-left px-4 py-2 border-b">Actions</th> <!-- kolchi dyal les actions -->
        </tr>
    </thead>
    <tbody>
        <!-- <?php foreach ($categories as $index => $cat): ?>
            <tr class="<?php echo $index % 2 == 0 ? 'bg-white' : 'bg-gray-200'; ?>">
                <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($cat['name']); ?></td>
                <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($cat['description']); ?></td>
                <td class="px-4 py-2 border-b">
                
            
                    <form action="admin_dashboard.php" method="POST" class="inline">
                        <input type="hidden" name="id_categorie" value="<?php echo $cat['id_categorie']; ?>">
                        <button type="submit" name="edit_category" class="bg-yellow-500 hover:bg-yellow-700 text-white  rounded">Modifier</button>
                    </form>
                    
                 
                    <form action="admin_dashboard.php" method="POST" class="inline">
                        <input type="hidden" name="id_categorie" value="<?php echo $cat['id_categorie']; ?>">
                        <button type="submit" name="delete_category" class="bg-red-500 hover:bg-red-700 text-white  rounded">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?> -->
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?php echo htmlspecialchars($cat['name']); ?></td>
                <td><?php echo htmlspecialchars($cat['description']); ?></td>
                <td>
                <button onclick="openCategoryModal(<?php echo htmlspecialchars(json_encode($cat)); ?>)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                <form action="admin_dashboard.php" method="POST" class="inline">
                     <input type="hidden" name="category_id" value="<?php echo $cat['id_categorie']; ?>">
                   <button type="submit" name="delete_category" class="text-red-500 hover:text-red-700" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');"><i class="fas fa-trash"></i></button>
                 </form>
                </td>
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
                            <!-- <p class="mb-4"><?php echo substr(htmlspecialchars($article['content']), 0, 200) .  '...'; ?></p> -->
                            <form action="admin_dashboard.php" method="POST" class="inline-block">
                                <input type="hidden" name="article_id" value="<?php echo $article['id_article']; ?>">
                                <button type="submit" name="approve_article" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2"><i class="fa-solid fa-check"></i></button>
                                <button type="submit" name="reject_article" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
</main>

 <!-- User Edit Modal -->
 <div id="userModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="admin_dashboard.php" method="POST">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="user_name">
                                Nom
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="user_name" type="text" name="user_name" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="user_email">
                                Email
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="user_email" type="email" name="user_email" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="user_role">
                                Rôle
                            </label>
                            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="user_role" name="user_role" required>
                                <option value="utilisateur">Utilisateur</option>
                                <option value="auteur">Auteur</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <input type="hidden" name="user_id" id="user_id">
                        <button type="submit" name="update_user" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Mettre à jour
                        </button>
                        <button type="button" onclick="closeUserModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
     
     <!-- Category Edit Modal -->
     <div id="categoryModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="admin_dashboard.php" method="POST">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
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
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <input type="hidden" name="category_id" id="category_id">
                        <button type="submit" name="update_category" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Mettre à jour
                        </button>
                        <button type="button" onclick="closeCategoryModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white shadow-lg text-white py-4 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2024 Cultures Partagées. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>

