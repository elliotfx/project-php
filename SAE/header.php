<?php
    $url = '/SAE';
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $url; ?>/css/header.css">
    <link rel="stylesheet" href="<?php echo $url; ?>/css/general.css">
    <link href="https://fonts.googleapis.com/css2?family=Permanent+Marker&display=swap" rel="stylesheet">

    <title>Votre Page</title>
</head>
<body>
    <?php
    session_start();
    $est_connecte = isset($_SESSION['user_id']);
    ?>

    <header>
        <div class="header-container">
            <a href="main.html"><img src="<?php echo $url; ?>/img/nba_logo.png" alt="Logo" class="logo"></a>
            <nav>
                <ul class="nav-buttons">
                    <li><a href="<?php echo $url; ?>/classement/php/classement.php" class="btn">Classement</a></li>
                    <li><a href="<?php echo $url; ?>/mycareer/php/mycarreer_home.php" class="btn">MyCareers</a></li>
                    <li><a href="<?php echo $url; ?>/vs/php/vs.php" class="btn">Versus</a></li>
                    <?php if ($est_connecte): ?>
                        <li><a href="<?php echo $url; ?>/php/deconnexion_user.php" class="btn">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $url; ?>/php/connexion_user.php"class="btn">Connexion</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
</body>
</html>
