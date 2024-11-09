<?php
    session_start(); 
    $est_connecte = isset($_SESSION['user_id']); 
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header avec Boutons</title>
    <link rel="stylesheet" href="./css/main_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Permanent+Marker&display=swap" rel="stylesheet">   
</head>
<body>
    
    <?php 
        include("./header.php")
    ?>
    <div class="Titre"><h1>CACALAND</h1></div>

</body>
</html>
