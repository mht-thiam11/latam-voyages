DROP DATABASE IF EXISTS AgenceVoyage;
CREATE DATABASE IF NOT EXISTS AgenceVoyage;
USE AgenceVoyage;

-- Table Clients mise à jour avec les informations de connexion
CREATE TABLE Clients (
    id_client INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    mot_de_passe VARCHAR(255) NOT NULL,  -- Pour stocker le mot de passe crypté
    role ENUM('client', 'admin') DEFAULT 'client'  -- Pour différencier les rôles des utilisateurs
);

-- Table Destinations
CREATE TABLE Destinations (
    id_destination INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    pays VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255)  -- Chemin de l'image associée
);


-- Table Voyages
CREATE TABLE Voyages (
    id_voyage INT PRIMARY KEY AUTO_INCREMENT,
    id_client INT,
    id_destination INT,
    date_depart DATE NOT NULL,
    date_retour DATE NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_client) REFERENCES Clients(id_client),
    FOREIGN KEY (id_destination) REFERENCES Destinations(id_destination)
);

-- Table Réservations
CREATE TABLE Reservations (
    id_reservation INT PRIMARY KEY AUTO_INCREMENT,
    id_client INT,
    id_voyage INT,
    date_reservation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('Confirmée', 'Annulée', 'En attente') DEFAULT 'En attente',
    FOREIGN KEY (id_client) REFERENCES Clients(id_client),
    FOREIGN KEY (id_voyage) REFERENCES Voyages(id_voyage)
);

-- Table Paiements
CREATE TABLE Paiements (
    id_paiement INT PRIMARY KEY AUTO_INCREMENT,
    id_reservation INT,
    montant DECIMAL(10,2) NOT NULL,
    date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    #mode_paiement ENUM('Carte', 'Virement', 'Espèces') NOT NULL,
    FOREIGN KEY (id_reservation) REFERENCES Reservations(id_reservation)
);

-- Table Hébergements
CREATE TABLE Hebergements (
    id_hebergement INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    adresse TEXT NOT NULL,
    type ENUM('Hôtel', 'Appartement', 'Maison Hote', 'Auberge') NOT NULL,
    prix_nuit DECIMAL(10,2) NOT NULL
);

-- Table Transport
CREATE TABLE Transport (
    id_transport INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('Avion', 'Train', 'Bus', 'Bateau') NOT NULL,
    compagnie VARCHAR(100),
    prix DECIMAL(10,2) NOT NULL
);

INSERT INTO Destinations (nom, pays, description) VALUES
('Buenos Aires', 'Argentine', 'Découvrez la capitale de l\'Argentine, riche en culture et histoire.'),
('Rio de Janeiro', 'Brésil', 'Plages magnifiques et carnaval dans la ville de Rio de Janeiro.'),
('Santiago', 'Chili', 'Venez explorer la capitale chilienne entre montagnes et océan.'),
('Bogotá', 'Colombie', 'Venez découvrir la capitale colombienne, un mélange de modernité et d\'histoire.'),
('Quito', 'Équateur', 'Visitez la capitale de l\'Équateur, connue pour sa vieille ville et sa proximité avec l\'équateur.'),
('Asunción', 'Paraguay', 'Explorez la capitale du Paraguay et son atmosphère unique.'),
('Lima', 'Pérou', 'La capitale péruvienne, avec ses plages, sa cuisine et son histoire précolombienne.'),
('Montevideo', 'Uruguay', 'Profitez de la douceur de vivre dans la capitale uruguayenne, Montevideo.'),
('Caracas', 'Venezuela', 'La capitale du Venezuela, riche en histoire et en beauté naturelle.'),
('Sucre', 'Bolivie', 'La ville historique de Sucre, centre de l\'histoire bolivienne.');

UPDATE Destinations SET image = 'images/buenos_aires.jpg' WHERE id_destination = 1;
UPDATE Destinations SET image = 'images/rio_de_janeiro.jpg' WHERE id_destination = 2;
UPDATE Destinations SET image = 'images/santiago.jpg' WHERE id_destination = 3;
UPDATE Destinations SET image = 'images/bogota.jpg' WHERE id_destination = 4;
UPDATE Destinations SET image = 'images/quito.jpg' WHERE id_destination = 5;
UPDATE Destinations SET image = 'images/asuncion.jpg' WHERE id_destination = 6;
UPDATE Destinations SET image = 'images/lima.jpg' WHERE id_destination = 7;
UPDATE Destinations SET image = 'images/montevideo.jpg' WHERE id_destination = 8;
UPDATE Destinations SET image = 'images/caracas.jpg' WHERE id_destination = 9;
UPDATE Destinations SET image = 'images/sucre.jpg' WHERE id_destination = 10;

ALTER TABLE Destinations
ADD distance INT; -- distance depuis Paris en kilomètres

UPDATE Destinations SET distance = 11050 WHERE id_destination = 1; -- Buenos Aires
UPDATE Destinations SET distance = 9170 WHERE id_destination = 2;  -- Rio de Janeiro
UPDATE Destinations SET distance = 11600 WHERE id_destination = 3; -- Santiago
UPDATE Destinations SET distance = 8650 WHERE id_destination = 4;  -- Bogotá
UPDATE Destinations SET distance = 9440 WHERE id_destination = 5;  -- Quito
UPDATE Destinations SET distance = 9800 WHERE id_destination = 6;  -- Asunción
UPDATE Destinations SET distance = 10200 WHERE id_destination = 7; -- Lima
UPDATE Destinations SET distance = 11050 WHERE id_destination = 8; -- Montevideo
UPDATE Destinations SET distance = 7550 WHERE id_destination = 9;  -- Caracas
UPDATE Destinations SET distance = 9600 WHERE id_destination = 10; -- Sucre
