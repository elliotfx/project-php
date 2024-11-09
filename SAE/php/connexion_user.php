<?php
session_start();
include("./connexion.php"); 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
<div class="container">
    <form method="POST" action="">
        <input type="text" name="pseudo" required placeholder="Pseudo" autocomplete="off" pattern="\S+" title="Pas d'espaces autorisés">
        <br>
        <input type="password" name="mdp" required placeholder="Mot de passe" autocomplete="off" pattern="\S+" title="Pas d'espaces autorisés">
        <br><br>
        <input type="submit" name="envoi" value="Se connecter">
    </form>

    <?php
    // Affichage des erreurs
    if (isset($error)) {
        echo '<p style="color: red;">' . htmlspecialchars($error) . '</p>';
    }
    ?>

    <div class="signup-link">
        <p>Pas de compte ? <a href="inscription.php">Créez-en un</a></p>
    </div>
</div>
</body>
</html>

<?php
try {
    // Vérifier si le formulaire de connexion a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["envoi"])) {
        // Récupérer les données du formulaire
        if (!empty($_POST['pseudo']) && !empty($_POST['mdp'])) {
            $pseudo = htmlspecialchars(trim($_POST['pseudo']));
            $mdp = trim($_POST['mdp']);

            // Préparer la requête pour obtenir l'utilisateur avec des paramètres liés
            $requete = "SELECT id, mdp FROM utilisateurs WHERE pseudo = :pseudo";
            $getUser  = $bdd->prepare($requete); 

            $getUser ->bindParam(':pseudo', $pseudo);

            $getUser ->execute();

            $user = $getUser ->fetch(PDO::FETCH_ASSOC); 

            // Si l'utilisateur existe, vérifier le mot de passe
            if ($user) {
                if (password_verify($mdp, $user['mdp'])) {
                    // Authentification réussie, enregistrer les infos dans la session
                    $_SESSION['pseudo'] = $pseudo;
                    $_SESSION['user_id'] = $user['id'];

                    // Redirection vers la page de bienvenue
                    header('Location: ./bienvenue.php');
                    exit; // Ajout d'un exit après la redirection
                
                } else {
                    $error = 'Mot de passe incorrect';
                }
            } else {
                $error = 'Pseudo incorrect';
            }
        } else {
            $error = 'Veuillez compléter tous les champs';
        }
    }

} catch (PDOException $e) {
    $error = 'Erreur de connexion à la base de données : ' . $e->getMessage();
}
?>