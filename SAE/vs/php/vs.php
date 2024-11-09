<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deux listes déroulantes</title>
</head>
<body>

    <form action="traitement.php" method="post">
        <!-- Première liste déroulante -->
        <label for="liste1">Choisir un élément de la première liste :</label>
        <select name="liste1" id="liste1" maxlength="10>
            <?php foreach ($options1 as $option): ?>
                <option value="<?= $option['id'] ?>"><?= $option['nom'] ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <!-- Deuxième liste déroulante -->
        <label for="liste2">Choisir un élément de la deuxième liste :</label>
        <select name="liste2" id="liste2">
            <?php foreach ($options2 as $option): ?>
                <option value="<?= $option['id'] ?>"><?= $option['description'] ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <button type="submit">Envoyer</button>
    </form>

</body>
<?php 
// Inclure le fichier de connexion à la base de données
include 'connexion.php';

// Récupérer les données pour la première liste déroulante
$query1 = $pdo->query("SELECT id, nom FROM table1");  // Remplacez 'table1' par le nom de votre table
$options1 = $query1->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les données pour la deuxième liste déroulante
$query2 = $pdo->query("SELECT id, description FROM table2");  // Remplacez 'table2' par le nom de votre autre table
$options2 = $query2->fetchAll(PDO::FETCH_ASSOC);
?>
</html>
