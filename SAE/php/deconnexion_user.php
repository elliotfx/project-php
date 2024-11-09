<?php
session_start(); // Démarre la session

// Détruire toutes les variables de session
session_unset();

// Détruire la session
session_destroy();

// Rediriger l'utilisateur vers la page de connexion ou une autre page
header('Location: ./connexion_user.php');
exit(); // Assurez-vous que le script s'arrête après la redirection
?>
