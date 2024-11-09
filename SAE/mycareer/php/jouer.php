<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jouer</title>
    <link rel="stylesheet" href="./../css/jouer.css">
</head>
<body>

<?php
session_start();

include('./../../header.php');
include('./../../php/connexion.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: ./../../php/connexion_user.php'); 
    exit;
}

if (isset($_GET['joueur'])) {
    $joueur = htmlspecialchars($_GET['joueur']);
    echo "<h1>$joueur</h1>";
} else {
    echo "<h1>Aucun joueur sélectionné.</h1>";
    exit;
}

$query = $bdd->prepare("SELECT AVG(GP) AS GP, AVG(MIN) AS MIN, AVG(FGM) AS FGM, AVG(FGA) AS FGA, AVG(PTS) AS PTS FROM mycareer WHERE id_utilisateur = :id AND nom_joueur = :nom");
$query->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$query->bindParam(':nom', $_GET['joueur'], PDO::PARAM_STR);
$query->execute();
$player_stats = $query->fetch(PDO::FETCH_ASSOC);

$avg_query = $bdd->query("SELECT AVG(GP) AS avg_GP, AVG(MIN) AS avg_MIN, AVG(FGM) AS avg_FGM, AVG(FGA) AS avg_FGA, AVG(PTS) AS avg_PTS FROM mycareer");
$avg_stats = $avg_query->fetch(PDO::FETCH_ASSOC);

$data = [
    'player' => [
        'GP' => $player_stats['GP'] ?? 0,
        'MIN' => $player_stats['MIN'] ?? 0,
        'FGM' => $player_stats['FGM'] ?? 0,
        'FGA' => $player_stats['FGA'] ?? 0,
        'PTS' => $player_stats['PTS'] ?? 0,
    ],
    'average' => [
        'GP' => $avg_stats['avg_GP'] ?? 0,
        'MIN' => $avg_stats['avg_MIN'] ?? 0,
        'FGM' => $avg_stats['avg_FGM'] ?? 0,
        'FGA' => $avg_stats['avg_FGA'] ?? 0,
        'PTS' => $avg_stats['avg_PTS'] ?? 0,
    ]
];
?>

<a href="mycarreer_home.php">Retour à la liste des joueurs</a>

<form method="POST" action="mycarreer_form.php">
    <input type="hidden" name="nom_joueur" value="<?php echo isset($joueur) ? htmlspecialchars($joueur) : ''; ?>">
    <button type="submit" name="envoyer">Nouveau Match</button>
</form>

<div id="container" style="width: 100%; height: 400px;"></div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const playerData = <?php echo json_encode($data, JSON_HEX_TAG); ?>;

    Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Comparaison des Statistiques du Joueur'
        },
        xAxis: {
            categories: ['Matchs Joués', 'Minutes', 'Buts Marqués', 'Buts Tentés', 'Points']
        },
        yAxis: {
            title: {
                text: 'Valeur'
            }
        },
        series: [{
            name: 'Votre Joueur',
            data: [
                playerData.player.GP,
                playerData.player.MIN,
                playerData.player.FGM,
                playerData.player.FGA,
                playerData.player.PTS
            ]
        }, {
            name: 'Moyenne des Autres Joueurs',
            data: [
                playerData.average.GP,
                playerData.average.MIN,
                playerData.average.FGM,
                playerData.average.FGA,
                playerData.average.PTS
            ]
        }]
    });
});
</script>

</body>
</html>
