CREATE DATABASE art_culture_platform;

USE art_culture_platform;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'author', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    user_id INT,
    category_id INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Insert some sample data
-- Additional users
INSERT INTO utilisateur (name, email, password, role) VALUES
('Yasmine El Amrani', 'yasmine@gmail.com', 'hashed_password6', 'auteur'),
('Karim Benali', 'karim@gmail.com', 'hashed_password7', 'utilisateur'),
('Leila Mansouri', 'leila@gmail.com', 'hashed_password8', 'auteur'),
('Omar Tazi', 'omar@gmail.com', 'hashed_password9', 'utilisateur'),
('Nadia Chaoui', 'nadia@gmail.com', 'hashed_password10', 'utilisateur');

-- Additional categories
INSERT INTO categories (name, description) VALUES
('Photographie', 'Articles sur la photographie, les photographes et les techniques'),
('Architecture', 'Articles sur l''architecture, les styles architecturaux et les architectes célèbres'),
('Danse', 'Articles sur la danse, les styles de danse et les danseurs célèbres'),
('Théâtre', 'Articles sur le théâtre, les pièces et les dramaturges'),
('Art numérique', 'Articles sur l''art numérique, les artistes et les nouvelles technologies');

-- Additional articles
INSERT INTO articles (title, content, id_categorie, id_auteur, status) VALUES
('L''évolution de la photographie numérique', 'La photographie numérique a révolutionné la façon dont nous capturons et partageons des images...', 6, 6, 'approved'),
('Les merveilles de l''architecture gothique', 'L''architecture gothique, avec ses arcs-boutants et ses vitraux, a marqué le Moyen Âge...', 7, 8, 'approved'),
('Le ballet contemporain : entre tradition et innovation', 'Le ballet contemporain repousse les limites de la danse classique...', 8, 3, 'pending'),
('Shakespeare : l''intemporalité du théâtre élisabéthain', 'Les pièces de Shakespeare continuent de captiver les audiences modernes...', 9, 2, 'approved'),
('L''art à l''ère du numérique', 'Les artistes numériques utilisent la technologie pour créer des œuvres interactives et immersives...', 10, 6, 'pending');
