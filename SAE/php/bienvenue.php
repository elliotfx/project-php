<?php
session_start();

// Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
if (!isset($_SESSION['pseudo'])) {
    header('Location: connexion_user.php');
    exit();
}



// Message de bienvenue
$pseudo = htmlspecialchars($_SESSION['pseudo']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
    <link rel="stylesheet" href="./style.css">
    <meta http-equiv="refresh" content="5;url=../main.php"> <!-- Redirige après 5 secondes -->
</head>
<body>
    <div class="container">
        <h1>Bienvenue, <?php echo $pseudo; ?>!</h1>
        <p>Vous êtes maintenant connecté.</p>
        <p>Vous serez redirigé vers la page principale dans 5 secondes.</p>
        <p>Si la redirection ne fonctionne pas, cliquez sur le lien ci-dessous :</p>
        <a href="../main.php">Aller à la page principale</a>
    </div>
</body>
</html>
