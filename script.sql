-- Création de la base de données
CREATE DATABASE cultures_partagees;


-- Table pour les utilisateurs
CREATE TABLE utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'auteur', 'utilisateur') ,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ADD COLUMN is_active BOOLEAN DEFAULT 1;
);

-- Table pour les catégories
CREATE TABLE categories (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour les articles
CREATE TABLE articles (
    id_article INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    id_categorie INT,
    id_auteur INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categorie) REFERENCES categories(id_categorie),
    FOREIGN KEY (id_auteur) REFERENCES utilisateur(id_utilisateur)
);



-- Insérer des utilisateur

INSERT INTO utilisateur (name, email, password, role)
VALUESf
('Mohamed Amin', 'mohamedamin@gmail.com', 'hashed_password1', 'admin'),
('Ali Hassan', 'alihassan@gmail.com', 'hashed_password2', 'auteur'),
('Fatima Zahra', 'fatimazahra@gmail.com', 'hashed_password3', 'auteur'),
('Ahmed Abdullah', 'ahmedabdullah@gmail.com', 'hashed_password4', 'utilisateur'),
('Sara Mahmoud', 'saramahmoud@gmail.com', 'hashed_password5', 'utilisateur');


-- Insérer des catégories


INSERT INTO categories (name, description)
VALUES
('Peinture', 'Articles sur la peinture, les artistes et les mouvements artistiques'),
('Musique', 'Articles sur la musique, les genres musicaux et les artistes'),
('Littérature', 'Articles sur la littérature, les écrivains et les genres littéraires'),
('Cinéma', 'Articles sur le cinéma, les films et les réalisateurs'),
('Sculpture', 'Articles sur la sculpture, les artistes et les techniques sculpturales');

-- Insérer des articles


INSERT INTO articles (title, content, id_categorie, id_auteur, status)
VALUES
('L’impressionnisme : Un regard nouveau sur la peinture', 'L’impressionnisme a marqué un tournant dans l’histoire de l’art...', 1, 2, 'approved'),
('Les grands classiques du jazz', 'Le jazz est un genre musical qui a influencé de nombreux autres styles...', 2, 3, 'approved'),
('La poésie française : Histoire et évolution', 'La poésie française a une longue tradition remontant au Moyen Âge...', 3, 2, 'pending'),
('Les films d’auteur : Une approche personnelle du cinéma', 'Le cinéma d’auteur est un genre cinématographique où la vision personnelle du réalisateur prédomine...', 4, 3, 'pending'),
('Les techniques modernes de sculpture', 'La sculpture moderne a évolué avec l’introduction de nouveaux matériaux et techniques...', 5, 2, 'approved');


---Delete 
delete from utilisateur 
where id_utilisateur = 1 ;


delete from categories 
where id_categorie = 1;


delete from articles 
where id_article = 1 ;



--update 

update utilisateur
set email = alihassan@gmail.com
where id_utilisateur = 2 ;



UPDATE categories 
SET NAME = 'Danse'
WHERE id_categorie = 2;


UPDATE categories 
SET  description = 'Articles sur la danse, les styles de danse et les danseurs célèbres'
WHERE id_categorie = 2;


-- Trouver le nombre total d'articles publiés par catégorie.

SELECT categories.name, COUNT(articles.id_article)
FROM categories
LEFT JOIN articles ON categories.id_categorie = articles.id_categorie
GROUP BY categories.id_categorie;


-- Identifier les auteurs les plus actifs en fonction du nombre d'articles publiés.

SELECT utilisateur.name AS auteur, COUNT(articles.id_article) AS nombre_articles
FROM utilisateur
LEFT JOIN articles ON utilisateur.id_utilisateur = articles.id_auteur
GROUP BY utilisateur.id_utilisateur
ORDER BY nombre_articles DESC;

-- Calculer la moyenne d'articles publiés par catégorie.

SELECT AVG(COUNT(articles.id_article))
FROM categories
LEFT JOIN articles ON categories.id_categorie = articles.id_categorie
GROUP BY categories.id_categorie;


-- Créer une vue affichant les derniers articles publiés dans les 30 derniers jours.

CREATE VIEW derniers_articles AS
SELECT articles.id_article, articles.title, articles.content, articles.created_at, categories.name
FROM articles
JOIN categories ON articles.id_categorie = categories.id_categorie
WHERE articles.created_at >= CURDATE() - INTERVAL 30 DAY;



-- Trouver les catégories qui n'ont aucun article associé

SELECT categories.id_categorie, categories.name
FROM categories
LEFT JOIN articles ON categories.id_categorie = articles.id_categorie
WHERE articles.id_article IS NULL;





