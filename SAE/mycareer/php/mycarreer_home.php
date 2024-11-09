<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Joueurs</title>
    <link rel="stylesheet" href="./../css/mycareer.css">
</head>
<body>

    <?php 
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: ./../../php/connexion_user.php'); 
        exit;
    }

    // Assurez-vous que $bdd est initialisé
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=php', 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }

    include('./../../header.php'); 

    // Traitement du formulaire
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['envoyer_joueur'])) {
        $nom_joueur = trim($_POST['nom_joueur_envoyer']);
        
        // Ajoutez le joueur à la base de données
        if (!empty($nom_joueur)) {
            $query = $bdd->prepare("
                INSERT INTO mycareer (nom_joueur, id_utilisateur) 
                VALUES (:nom_joueur, :id_utilisateur)
            ");
            $query->bindParam(':nom_joueur', $nom_joueur, PDO::PARAM_STR);
            $query->bindParam(':id_utilisateur', $_SESSION['user_id'], PDO::PARAM_INT);
            $query->execute();
            
            // Redirection après l'ajout
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }

    // Compter le nombre de joueurs de l'utilisateur
    $query = $bdd->prepare("
        SELECT COUNT(*)
        FROM mycareer 
        WHERE id_utilisateur = :id_utilisateur
    ");
    $query->bindParam(':id_utilisateur', $_SESSION['user_id'], PDO::PARAM_INT);
    $query->execute();
    $resultCount = $query->fetchColumn();

    if ($resultCount < 5) {
        // Afficher le formulaire pour ajouter un joueur
        ?>
        <form method="POST" action="">
            <input type="text" id="nom_joueur_envoyer" name="nom_joueur_envoyer" maxlength="50" required>
            <button type="submit" name="envoyer_joueur">Ajouter un Joueur</button>
        </form>
        <?php
    } else {
        echo "<p>Vous avez atteint le maximum de joueurs.</p>";
    }

    // Récupérer et afficher tous les joueurs
    $query = $bdd->prepare("
        SELECT nom_joueur
        FROM mycareer 
        WHERE id_utilisateur = :id_utilisateur
    ");
    $query->bindParam(':id_utilisateur', $_SESSION['user_id'], PDO::PARAM_INT);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    // Conteneur pour les joueurs
    echo '<div class="container">';
    foreach ($results as $row) {
        $nom_joueur = htmlspecialchars($row['nom_joueur']);
        echo '<div class="player_container" onclick="location.href=\'Jouer.php?joueur=' . urlencode($nom_joueur) . '\'">';
        echo '<div class="player">' . $nom_joueur . '</div>';
        echo '</div>';
    }
    echo '</div>'; // Fin du conteneur
    echo "<h1>Bienvenue, utilisateur ID: " . htmlspecialchars($_SESSION['user_id']) . "</h1>";

    ?>

</body>
</html>