# project-php


Structure de ma BDD Easy-Php :

Table Utilisateurs : 

CREATE TABLE IF NOT EXISTS utilisateurs(
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,  -- Ajout de la colonne id avec AUTO_INCREMENT,
    pseudo VARCHAR(50),
    mdp VARCHAR(255)
); 

