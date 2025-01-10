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

ALTER TABLE articles ADD image_url VARCHAR(255) NOT NULL;




-- Insérer des utilisateur

INSERT INTO utilisateur (name, email, password, role)
VALUES
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

SELECT COUNT(articles.id_article) , categories.name
FROM categories 
JOIN articles
ON  categories.id_categorie = articles.id_categorie
GROUP BY categories.name
ORDER BY COUNT(articles.id_article) DESC


-- Identifier les auteurs les plus actifs en fonction du nombre d'articles publiés.

SELECT utilisateur.name, COUNT(articles.id_article)
FROM utilisateur
JOIN articles ON utilisateur.id_utilisateur = articles.id_auteur
GROUP BY utilisateur.name
ORDER BY COUNT(articles.id_article) DESC;


-- Calculer la moyenne d'articles publiés par catégorie.

SELECT AVG(COUNT(articles.id_article))
FROM categories
LEFT JOIN articles ON categories.id_categorie = articles.id_categorie
GROUP BY categories.id_categorie;
-- ====
SELECT AVG(articles.id_article) , categories.name
FROM articles 
JOIN categories
ON categories.id_categorie = articles.id_categorie
GROUP BY  categories.name
ORDER BY AVG(articles.id_article)

-- Créer une vue affichant les derniers articles publiés dans les 30 derniers jours.



CREATE VIEW derniers_articles
SELECT id_article, title, content, id_categorie, id_auteur, created_at
FROM articles
WHERE created_at >= NOW() - INTERVAL 30 DAY;




-- Trouver les catégories qui n'ont aucun article associé

SELECT categories.id_categorie, categories.name
FROM categories
LEFT JOIN articles ON categories.id_categorie = articles.id_categorie
WHERE articles.id_article IS NULL;



-- =================

ALTER TABLE utilisateur ADD COLUMN profile_picture VARCHAR(255);
ALTER TABLE utilisateur ADD COLUMN is_active TINYINT(1) DEFAULT 1;

CREATE TABLE likes (
    id_like INT AUTO_INCREMENT PRIMARY KEY,
    id_article INT,
    id_utilisateur INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_article) REFERENCES articles(id_article),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE favorites (
    id_favorite INT AUTO_INCREMENT PRIMARY KEY,
    id_article INT,
    id_utilisateur INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_article) REFERENCES articles(id_article),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE comments (
    id_comment INT AUTO_INCREMENT PRIMARY KEY,
    id_article INT,
    id_utilisateur INT,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_article) REFERENCES articles(id_article),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE tags (
    id_tag INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE
);

CREATE TABLE article_tags (
    id_article INT,
    id_tag INT,
    PRIMARY KEY (id_article, id_tag),
    FOREIGN KEY (id_article) REFERENCES articles(id_article),
    FOREIGN KEY (id_tag) REFERENCES tags(id_tag)
);

ALTER TABLE articles 
DROP FOREIGN KEY articles_ibfk_1;

-- Ajoutez la nouvelle contrainte avec ON DELETE CASCADE
ALTER TABLE articles
ADD CONSTRAINT articles_ibfk_1 
FOREIGN KEY (id_categorie) 
REFERENCES categories(id_categorie) 
ON DELETE CASCADE;


-- Afficher les articles les plus likés avec leur titre, le nombre de likes, et leur catégorie.

SELECT articles.title, categories.name, COUNT(likes.id_like)
FROM articles
JOIN categories ON articles.id_categorie = categories.id_categorie
LEFT JOIN likes ON articles.id_article = likes.id_article
GROUP BY articles.id_article, categories.name
ORDER BY COUNT(likes.id_like) DESC;


-- Mettre à jour automatiquement le statut is_active d’un utilisateur à 0 (banni) après une action d’administration.

CREATE TRIGGER bannir_utilisateur 
AFTER UPDATE ON utilisateur
FOR EACH ROW
BEGIN
    IF NEW.role = 'banni' THEN
        UPDATE utilisateur 
        SET is_active = 0 
        WHERE id_utilisateur = NEW.id_utilisateur;
    END IF;
END;


-- Identifier les tags les plus associés aux articles publiés au cours des 30 derniers jours, en affichant le nom du tag et le nombre d’associations.

SELECT tags.name, COUNT(article_tags.id_tag) AS nombre_associations
FROM tags
JOIN article_tags ON tags.id_tag = article_tags.id_tag
JOIN articles ON article_tags.id_article = articles.id_article
WHERE articles.created_at >= NOW() - INTERVAL 30 DAY
GROUP BY tags.name
ORDER BY nombre_associations DESC;
