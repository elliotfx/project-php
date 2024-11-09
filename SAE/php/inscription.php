<?php
session_start();
include("./connexion.php"); 

// Récupération des données du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifiez si les champs sont bien remplis
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];

    // Validation basique des données
    if (!empty($username) && !empty($password)) {
        // Hashage du mot de passe
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Vérification si l'utilisateur existe déjà avec ce pseudo
        $stmt = $bdd->prepare("SELECT * FROM utilisateurs WHERE pseudo = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $error = "Ce pseudo est déjà utilisé.";
        } else {
            // Insertion de l'utilisateur dans la base de données
            $sql = "INSERT INTO utilisateurs (pseudo, mdp) VALUES (?, ?)";
            $stmt = $bdd->prepare($sql);
            try {
                if ($stmt->execute([$username, $password_hashed])) {
                    // Récupérer l'ID de l'utilisateur nouvellement créé
                    $userId = $bdd->lastInsertId();

                    // Connecter l'utilisateur en enregistrant ses informations dans la session
                    $_SESSION['pseudo'] = $username;
                    $_SESSION['user_id'] = $userId;

                    // Redirection vers bienvenue.php ou votre page d'accueil
                    header('Location: ./bienvenue.php');
                    exit(); // N'oubliez pas de mettre exit() après une redirection
                } else {
                    $error = "Erreur lors de l'insertion.";
                }
            } catch (PDOException $e) {
                $error = "Erreur lors de l'insertion : " . $e->getMessage();
            }
        }
    } else {
        $error = "Tous les champs sont obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
<div class="container">
    <h2>Inscription</h2>
    
    <form method="POST" action="">
        <label for="username">Pseudo:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required>
        
        <input type="submit" value="S'inscrire">
    </form>

    <?php
    // Affichage des messages d'erreur ou de succès
    if (isset($error)) {
        echo '<p class="error">' . htmlspecialchars($error) . '</p>';
    }
    ?>
</div>
</body>
</html>
