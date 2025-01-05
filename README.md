La plateforme vise à promouvoir l’art et la culture en offrant aux utilisateurs un espace pour publier des articles sur des sujets divers tels que la peinture, la musique, la littérature, le cinéma, et bien d'autres. Elle doit fournir une interface fluide pour les utilisateurs, ainsi qu’un Dashboard pour l'administration, permettant une gestion efficace des utilisateurs, des catégories, et des articles.
![Screenshot 2025-01-05 231352](https://github.com/user-attachments/assets/6dfd7c23-c609-4f69-942a-37f80a4153bd)
![Screenshot 2025-01-05 231436](https://github.com/user-attachments/assets/436ff867-ca2b-4f9a-a287-2b0bb5366ce0)

Le code que vous avez partagé est un formulaire d'inscription qui permet aux utilisateurs de s'enregistrer sur la plateforme "Cultures Partagées". Ce formulaire contient des champs pour le nom, l'email, le mot de passe et la confirmation du mot de passe. Il inclut également une gestion des erreurs pour valider les informations saisies. Voici un aperçu détaillé de chaque partie du code :

Partie PHP :
Vérification du formulaire :
Lorsqu'un utilisateur soumet le formulaire, le code vérifie si tous les champs sont remplis (name, email, password, confirm_password).
Si les mots de passe ne correspondent pas, un message d'erreur est affiché.
Si les informations sont valides, l'utilisateur est inscrit via la méthode register() de la classe User
2.Redirection :
Si l'inscription réussit, l'utilisateur est redirigé vers la page de connexion (login.php) avec un message de succès.
Si une erreur survient lors de l'inscription, un message d'erreur est affiché

![Screenshot 2025-01-05 231330](https://github.com/user-attachments/assets/a21e8e3e-d1be-4fa2-be8c-bc6eba4f96b4)
![Screenshot 2025-01-05 232222](https://github.com/user-attachments/assets/f7a091c2-8c09-4c90-a9e1-fcf917348a8d)
Partie PHP :
Vérification des informations de connexion:
Lorsque l'utilisateur soumet le formulaire, le code vérifie si les informations de connexion (email et mot de passe) sont valides.
La méthode login() de la classe User est appelée pour vérifier si l'email et le mot de passe correspondent à un utilisateur existant dans la base de données.
Si la connexion est réussie, les informations de l'utilisateur (ID, nom et rôle) sont stockées dans la session, et l'utilisateur est redirigé vers la page index.php.

2.Partie HTML :
Structure du Formulaire :
Le formulaire contient deux champs : un pour l'email et un autre pour le mot de passe.
Chaque champ a une étiquette (label) et un champ de saisie (input), avec des styles modernes grâce à Tailwind CSS.

3.Messages d'Erreur et de Succès :
Si une erreur survient (par exemple, un mauvais email ou mot de passe), un message d'erreur est affiché en haut du formulaire, avec une bordure rouge pour attirer l'attention de l'utilisateur.
Si un message de succès est défini dans la session (par exemple, après une inscription réussie), un message vert est affiché pour informer l'utilisateur.

![Screenshot 2025-01-05 225912](https://github.com/user-attachments/assets/e78822da-1b7a-41e2-ba33-a0c131e419f7)
![Screenshot 2025-01-05 231034](https://github.com/user-attachments/assets/de3acf0c-acdb-49da-8ecf-e8e54e0c0e65)
![Screenshot 2025-01-05 230158](https://github.com/user-attachments/assets/f7176f35-35ba-405f-93be-2b166f5c992b)


Le code que vous avez partagé est une page de tableau de bord administrateur pour le site "Cultures Partagées". Voici un aperçu de ce que chaque section du code réalise :
Fonctionnalités principales :
Authentification et autorisation de l'utilisateur :
Vérifie si l'utilisateur est connecté et possède le rôle d'administrateur ($_SESSION['user_role'] !== 'admin').
Si non, l'utilisateur est redirigé vers la page d'accueil (index.php).
Gestion des utilisateurs :
Récupère et affiche la liste des utilisateurs avec leur nom, email, et rôle dans un tableau.
Gestion des catégories :
Permet à l'administrateur de créer de nouvelles catégories avec un formulaire qui contient un champ pour le nom et la description.
Après la création, la liste des catégories est mise à jour.

Gestion des articles en attente :
Affiche les articles en attente d'approbation avec des options pour approuver ou rejeter chaque article.
Un bouton est fourni pour chaque article afin de l'approuver (bouton vert) ou le rejeter (bouton rouge).
Messages de succès ou d'erreur :
Affiche des alertes pour indiquer si une action a été réussie ou a échoué (par exemple, création de catégorie, approbation d'article).

Structure HTML et CSS (avec Tailwind) :
En-tête : Un en-tête avec une navigation qui inclut des liens vers la page d'accueil et la déconnexion.
Tableaux : Utilisation de tableaux pour afficher les utilisateurs, les catégories, et les articles en attente, avec une alternance de couleurs pour les lignes des tableaux.
Formulaire de création de catégorie : Un formulaire simple pour ajouter des catégories avec un bouton pour soumettre les données.
Notifications : Des messages de succès et d'erreur sont affichés dans des boîtes colorées avec Tailwind CSS.
Points à vérifier :
Sécurité :
Le formulaire utilise $_POST pour recevoir des données. Assurez-vous que toutes les données envoyées sont validées et assainies pour éviter les attaques de type XSS ou injection SQL.
L'utilisation de $_SESSION pour vérifier l'identité de l'administrateur est bonne, mais vous pourriez aussi envisager une vérification supplémentaire comme un jeton CSRF pour protéger contre les attaques de type cross-site request forgery.








