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