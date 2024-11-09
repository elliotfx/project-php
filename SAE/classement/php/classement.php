<?php
session_start();

include './../../header.php';
include './../../connexion.php';

// Affiche un message de bienvenue avec l'ID de l'utilisateur
echo "Bienvenue, utilisateur ID: " . $_SESSION['user_id'];



// Liste des colonnes disponibles pour le tri
$sortable_columns = [
    'MIN' => 'Minutes Jouées',
    'PTS' => 'Points',
    'REB' => 'Rebonds',
    'AST' => 'Passes',
    'STL' => 'Interceptions',
    'BLK' => 'Contres'
];

// Définit la colonne de tri par défaut
$order_by = 'MIN';

// Si une colonne est sélectionnée dans le formulaire, la récupère et la valide
if (isset($_GET['sort']) && array_key_exists($_GET['sort'], $sortable_columns)) {
    $order_by = $_GET['sort'];
}

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id']; // Assurez-vous que l'ID utilisateur est dans la session
    $sql = "
        SELECT * 
        FROM mycareer
        WHERE user_id = :user_id
        UNION ALL
        SELECT *
        FROM classement
        WHERE user_id = :user_id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

}
else
{
// Récupère les données triées par la colonne sélectionnée en ordre décroissant
$sql = "SELECT * FROM joueurs ORDER BY $order_by DESC LIMIT 50";
$result = $conn->query($sql);
}


// Démarre la sortie HTML
echo "<html>";
echo "<head><link rel='stylesheet' type='text/css' href='styles.css'></head>";
echo "<body>";

// Formulaire de sélection de tri
echo "<form method='GET'>";
echo "<label for='sort'>Trier par : </label>";
echo "<select name='sort' id='sort' onchange='this.form.submit()'>";
foreach ($sortable_columns as $column => $label) {
    $selected = ($order_by == $column) ? "selected" : "";
    echo "<option value='$column' $selected>$label</option>";
}
echo "</select>";
echo "</form>";

// Affiche la table des joueurs
echo "<table border='1'>
        <tr>
            <th>Rank</th>
            <th>ID</th>
            <th>League</th>
            <th>Season</th>
            <th>Stage</th>
            <th>Player</th>
            <th>Team</th>
            <th>GP</th>
            <th>MIN</th>
            <th>FGM</th>
            <th>FGA</th>
            <th>3PM</th>
            <th>3PA</th>
            <th>FTM</th>
            <th>FTA</th>
            <th>TOV</th>
            <th>PF</th>
            <th>ORB</th>
            <th>DRB</th>
            <th>REB</th>
            <th>AST</th>
            <th>STL</th>
            <th>BLK</th>
            <th>PTS</th>
            <th>Birth Date</th>
            <th>Height (cm)</th>
            <th>Weight (kg)</th>
            <th>Nationality</th>
            <th>High School</th>
            <th>Draft Round</th>
            <th>Draft Pick</th>
            <th>Draft Team</th>
        </tr>";

// Initialise le rang
$rank = 1;

// Affiche les données si elles existent
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $rank . "</td>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['League'] . "</td>";
        echo "<td>" . $row['Season'] . "</td>";
        echo "<td>" . $row['Stage'] . "</td>";
        echo "<td>" . $row['Player'] . "</td>";
        echo "<td>" . $row['Team'] . "</td>";
        echo "<td>" . $row['GP'] . "</td>";
        echo "<td>" . $row['MIN'] . "</td>";
        echo "<td>" . $row['FGM'] . "</td>";
        echo "<td>" . $row['FGA'] . "</td>";
        echo "<td>" . $row['3PM'] . "</td>";
        echo "<td>" . $row['3PA'] . "</td>";
        echo "<td>" . $row['FTM'] . "</td>";
        echo "<td>" . $row['FTA'] . "</td>";
        echo "<td>" . $row['TOV'] . "</td>";
        echo "<td>" . $row['PF'] . "</td>";
        echo "<td>" . $row['ORB'] . "</td>";
        echo "<td>" . $row['DRB'] . "</td>";
        echo "<td>" . $row['REB'] . "</td>";
        echo "<td>" . $row['AST'] . "</td>";
        echo "<td>" . $row['STL'] . "</td>";
        echo "<td>" . $row['BLK'] . "</td>";
        echo "<td>" . $row['PTS'] . "</td>";
        echo "<td>" . $row['birth_date'] . "</td>";
        echo "<td>" . $row['height_cm'] . "</td>";
        echo "<td>" . $row['weight_kg'] . "</td>";
        echo "<td>" . $row['nationality'] . "</td>";
        echo "<td>" . $row['high_school'] . "</td>";
        echo "<td>" . $row['draft_round'] . "</td>";
        echo "<td>" . $row['draft_pick'] . "</td>";
        echo "<td>" . $row['draft_team'] . "</td>";
        echo "</tr>";
        $rank++;
    }
} else {
    echo "<tr><td colspan='31'>Aucun enregistrement trouvé</td></tr>";
}

// Fin de la table et de la page HTML
echo "</table>";
echo "</body>";
echo "</html>";




// Ferme la connexion
$conn->close();
?>
