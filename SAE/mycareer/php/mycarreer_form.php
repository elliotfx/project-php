<?php
    session_start();

    // -------------------------------Logique Statistique------------------------------------------------------------
    include("./../../php/connexion.php");

    $id_utilisateur = $_SESSION['user_id']; // À changer après que la connexion soit ok

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Traitement du formulaire "Ajouter un Joueur"
        if (isset($_POST['envoyer_joueur']) && !empty($_POST['nom_joueur_envoyer'])) {
            
            // Préparation de la requête pour vérifier si le joueur existe déjà
            $query = $bdd->prepare("
            SELECT nom_joueur
            FROM mycareer 
            WHERE nom_joueur = :nom_joueur
            AND id_utilisateur = :id ");
    
            $query->bindParam(':nom_joueur', $_POST['nom_joueur_envoyer']);
            $query->bindParam(':id', $_SESSION['user_id']);
            $query->execute();
            $nom = $query->fetch(PDO::FETCH_ASSOC);
    
            // Vérification si le joueur existe déjà
            if ($nom && $nom['nom_joueur'] === $_POST['nom_joueur_envoyer']) {
                echo "Le joueur existe déjà";
                exit();
            } else {
                echo AjoutJoueur($_POST['nom_joueur_envoyer']);
            }
    
            // Redirection pour éviter la re-soumission en cas de rafraîchissement
            exit();
        }
    
        // Traitement du formulaire "Nouveau Match"
        if (isset($_POST['envoyer']) && !empty($_POST['nom_joueur'])) {
            nouveauxMatch($_POST['nom_joueur'], $id_utilisateur);
    
            // Redirection pour éviter la re-soumission en cas de rafraîchissement
            exit();
        }
    }
    


    function nouveauxMatch($nom_joueur, $id_utilisateur) {
        global $bdd; // Assurez-vous que $bdd est bien défini dans connexion.php

        // Récupérer toutes les données nécessaires en une seule requête
        $query = $bdd->prepare("
            SELECT 
                Probas_1_3, Probas_4_5, Probas_5_10, Probas_11_15, Probas_16_20, Probas_21_30, Probas_31_35, Probas_36_40,
                Probas_Nul, Probas_Pas_Fou, Probas_Moyen, Probas_Bien, Probas_Exellent, taille_joueur
            FROM mycareer 
            WHERE nom_joueur = :nom_joueur
        ");
        $query->bindParam(':nom_joueur', $nom_joueur);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        // Vérifiez si les résultats existent
        if ($result) {

            $taille = $result['taille_joueur'];
            // Déterminer les probabilités cumulatives pour le temps de jeu
            // Calcul des probabilités cumulatives

            $proba_ranges = [
                '1_3' => $result['Probas_1_3'],  // Probabilité du premier intervalle
                '4_5' => $result['Probas_1_3'] + $result['Probas_4_5'],  // Cumul des 1_3 et 4_5
                '5_10' => $result['Probas_1_3'] + $result['Probas_4_5'] + $result['Probas_5_10'],  // Cumul jusqu'à 5_10
                '11_15' => $result['Probas_1_3'] + $result['Probas_4_5'] + $result['Probas_5_10'] + $result['Probas_11_15'],  // Cumul jusqu'à 11_15
                '16_20' => $result['Probas_1_3'] + $result['Probas_4_5'] + $result['Probas_5_10'] + $result['Probas_11_15'] + $result['Probas_16_20'],  // Cumul jusqu'à 16_20
                '21_30' => $result['Probas_1_3'] + $result['Probas_4_5'] + $result['Probas_5_10'] + $result['Probas_11_15'] + $result['Probas_16_20'] + $result['Probas_21_30'],  // Cumul jusqu'à 21_30
                '31_35' => $result['Probas_1_3'] + $result['Probas_4_5'] + $result['Probas_5_10'] + $result['Probas_11_15'] + $result['Probas_16_20'] + $result['Probas_21_30'] + $result['Probas_31_35'],  // Cumul jusqu'à 31_35
                '36_40' => $result['Probas_1_3'] + $result['Probas_4_5'] + $result['Probas_5_10'] + $result['Probas_11_15'] + $result['Probas_16_20'] + $result['Probas_21_30'] + $result['Probas_31_35'] + $result['Probas_36_40'],

            ];
            


        // Tirer un nombre aléatoire entre 1 et 100 pour la probabilité
        $proba = (random_int(1, 100))/100;

        $classe = '';
        $tps_jeu = 0;

        // Déterminer la classe du joueur et le temps de jeu
        foreach ($proba_ranges as $key => $value) {
            if ($proba <= $value) {
                $classe = $key;
                        
                // Définir le temps de jeu en fonction de l'intervalle
                switch ($key) {
                    case '1_3':
                        $tps_jeu = random_int(1, 3);
                        break;
                    case '4_5':
                        $tps_jeu = random_int(4, 5);
                        break;
                    case '5_10':
                        $tps_jeu = random_int(5, 10);
                        break;
                    case '11_15':
                        $tps_jeu = random_int(11, 15);
                        break;
                    case '16_20':
                        $tps_jeu = random_int(16, 20);
                        break;
                    case '21_30':
                        $tps_jeu = random_int(21, 30);
                        break;
                    case '31_35':
                        $tps_jeu = random_int(31, 35);
                        break;
                    case '36_40':
                        $tps_jeu = random_int(36, 40);
                        break;
                }
        
                // Une fois que la classe est définie, on sort de la boucle
                break;
            }
        }
        

            // Affichage des résultats pour vérification
            echo "Classe finale : " . $classe . "<br>";
            echo "Temps de jeu : " . $tps_jeu . "<br>";


            // Calcul du nombre de points

            $proba_ranges2 = [
                'Nul' => $result['Probas_Nul'],
                'Pas Fou' => $result['Probas_Nul'] + $result['Probas_Pas_Fou'],
                'Moyen' => $result['Probas_Nul'] + $result['Probas_Pas_Fou'] + $result['Probas_Moyen'],
                'Bien' => $result['Probas_Nul'] + $result['Probas_Pas_Fou'] + $result['Probas_Moyen'] + $result['Probas_Bien'],
                'Exellent' => 1, // Toujours 100 pour couvrir toutes les probabilités
            ];

            print_r($proba_ranges2);


            $random_number = (random_int(1, 100))/100;
            $nbpointstot = 0;

            if ($random_number <= $proba_ranges2['Nul']) {
                $nbpointstot = round(random_int(5, 20) / 100 * $tps_jeu); 
                $trois_pts_accuracy = random_int(8,12);
                $deux_points_accuracy = random_int(4,7);
                $LF_accuracy = random_int(2,5);
                $block_nb = 0 ;
				$steal = 0 ;
				$assist = floor(random_int(5,10)/100);
                $turnovers_nb = floor((random_int(70,100)/100)*$tps_jeu);
                $foul_nb = random_int(1,4);
                $rebonds_nb = Calculer_Rebonds('Nul', $taille);
                $assist_nb = random_int(0,2);
                $steal_nb = random_int(0,2);
                $niveau = 'Nul';


            } elseif ($random_number <= $proba_ranges2['Pas Fou']) {
                $nbpointstot = round(random_int(20, 40) / 100 * $tps_jeu); 
                $trois_pts_accuracy = random_int(5, 9);
                $deux_points_accuracy = random_int(3, 5);
                $LF_accuracy = random_int(2, 4);
                $block_nb = floor(random_int(20, 50) / 1000 * $tps_jeu); 
                $steal = floor(random_int(40, 60) / 1000 * $tps_jeu); 
                $assist = floor(random_int(10, 30) / 100);
                $turnovers_nb = floor((random_int(50, 80) / 100) * $tps_jeu);
                $foul_nb = random_int(1, 3);
                $rebonds_nb = Calculer_Rebonds('Pas Fou', $taille);
                $assist_nb = random_int(0, 3);
                $steal_nb = random_int(0, 3);
                $niveau = 'Pas Fou';

            } elseif ($random_number <= $proba_ranges2['Moyen']) {
                $nbpointstot = round(random_int(30, 50) / 100 * $tps_jeu); 
                $trois_pts_accuracy = random_int(4, 6);
                $deux_points_accuracy = random_int(2, 4);
                $LF_accuracy = random_int(2, 3);
                $block_nb = floor(random_int(50, 80) / 1000 * $tps_jeu); 
                $steal = floor(random_int(60, 90) / 1000 * $tps_jeu); 
                $assist = random_int(30, 50) / 100;
                $turnovers_nb = floor((random_int(25, 35) / 100) * $tps_jeu);
                $foul_nb = random_int(1, 5);
                $rebonds_nb = Calculer_Rebonds('Moyen', $taille);
                $assist_nb = random_int(1, 5);
                $steal_nb = random_int(1, 4);
                $niveau = 'Moyen';


            } elseif ($random_number <= $proba_ranges2['Bien']) {
                $nbpointstot = round(random_int(51, 60) / 100 * $tps_jeu); 
                $trois_pts_accuracy = random_int(150, 250) / 100; 
                $deux_points_accuracy = random_int(130, 160) / 100; 
                $LF_accuracy = random_int(140, 300) / 100; 
                $block_nb = floor(random_int(10, 15) / 100 * $tps_jeu); 
                $steal = floor(random_int(13, 20) / 100 * $tps_jeu); 
                $turnovers_nb = floor(random_int(50, 100) / 1000 * $tps_jeu); 
                $foul_nb = random_int(0, 5);
                $rebonds_nb = Calculer_Rebonds('Bien', $taille);
                $assist_nb = random_int(4, 9);
                $steal_nb = random_int(2, 5); 
                $niveau = 'Bien';

            } else {
                $nbpointstot = round(random_int(61, 150) / 100 * $tps_jeu); 
                $trois_pts_accuracy = random_int(100, 140) / 100; 
                $deux_points_accuracy = random_int(100, 130) / 100; 
                $LF_accuracy = random_int(100, 120) / 100; 
                $block_nb = floor(random_int(15, 25) / 100 * $tps_jeu); 
                $steal = floor(random_int(17, 30) / 100 * $tps_jeu); 
                $turnovers_nb = floor(random_int(20, 40) / 1000 * $tps_jeu); 
                $foul_nb = random_int(0, 5);
                $rebonds_nb = Calculer_Rebonds('Exellent', $taille);
                $assist_nb = random_int(5, 12);
                $steal_nb = random_int(4, 7);
                $niveau = 'Exellent';
                

            }
            

            
            $total_panier = floor($nbpointstot);
            $panier_3_points = floor(($total_panier * 0.1)/3);
            $panier_2_points = floor(($total_panier * 0.7)/2);
            $panier_lancers_francs = floor($total_panier * 0.2);

            $pts_2_tentes = round($panier_2_points * $deux_points_accuracy); 
            $pts_3_tentes = round($panier_3_points * $trois_pts_accuracy);   
            $LF_tente = round($panier_lancers_francs * $LF_accuracy);

            $reb_def = $rebonds_nb[0];
            $reb_off = $rebonds_nb[1];
            $nb_rebonds = (int)($reb_def + $reb_off); // Total des rebonds converti en entier

            




            // Ajustement du total
            $total_points_calcules = ($panier_3_points * 3) + ($panier_2_points * 2) + $panier_lancers_francs;


            while ($total_points_calcules > $total_panier) {
                if ($panier_lancers_francs > 0) {
                    $panier_lancers_francs--;
                } elseif ($panier_2_points > 0) {
                    $panier_2_points--;
                    if ($total_points_calcules < $total_panier){
                        while($total_points_calcules < $total_panier){
                            $panier_lancers_francs++;
                            $total_points_calcules = ($panier_3_points * 3) + ($panier_2_points * 2) + $panier_lancers_francs;
                        }
                    }

                    
                }
                $total_points_calcules = ($panier_3_points * 3) + ($panier_2_points * 2) + $panier_lancers_francs;
                echo $total_points_calcules;
            }

            while ($total_points_calcules < $total_panier) {
                if ($panier_lancers_francs < $total_panier) { // S'assurer de ne pas dépasser le total
                    $panier_lancers_francs++;
                } else if ($panier_2_points < $total_panier / 2) { // Limiter les paniers à 2 points
                    $panier_2_points++;
                } else if ($panier_3_points < $total_panier / 3) { // Limiter les paniers à 3 points
                    $panier_3_points++;
                }
                $total_points_calcules = ($panier_3_points * 3) + ($panier_2_points * 2) + $panier_lancers_francs;
                echo "<br>";
                echo $total_points_calcules;
            } 

             // ---------- Calcul % de réussite ----------------

            $nbtirs_3pts = floor($panier_3_points * $trois_pts_accuracy);
            $nbtirs_2pts = floor($panier_2_points * $deux_points_accuracy);
            $nbtirs_LF = floor($panier_lancers_francs * $LF_accuracy);


            // Vérifiez que les valeurs ne sont pas négatives
            $panier_3_points = max(0, $panier_3_points);
            $panier_2_points = max(0, $panier_2_points);
            $panier_lancers_francs = max(0, $panier_lancers_francs);

            Change_Proba($proba_ranges, $niveau, $tps_jeu, $panier_2_points, $pts_2_tentes, $panier_3_points, $pts_3_tentes, $panier_lancers_francs, $LF_tente, $turnovers_nb, $foul_nb, $reb_off, $reb_def, $nb_rebonds, $assist_nb, $steal_nb, $block_nb, $nbpointstot, $id_utilisateur, $nom_joueur);

            

            // Affichage des résultats
            echo "Classe: $classe<br>";
            echo "Temps de jeu: $tps_jeu minutes<br>";
            echo "Points totaux: $total_panier<br>";
            echo "Paniers à 3 points: $panier_3_points<br>";
            echo "Paniers à 2 points: $panier_2_points<br>";
            echo "Lancers francs: $panier_lancers_francs<br>";

            echo "3pts tentés: $nbtirs_3pts<br>";
            echo "2pts tentés: $nbtirs_2pts<br>";
            echo "LF tentés: $nbtirs_LF<br>";

                // -------------------------------------------------- Ajout de Joueurs -------------------------------------------------
            
    }}

    function AjoutJoueur($nom_joueur_envoyer) {
        global $bdd;
        $id_utilisateur = $_SESSION['user_id'];
        
        try {
            $query = $bdd->prepare("
            INSERT INTO mycareer (
                `id_utilisateur`, 
                `nom_joueur`, 
                `sexe_joueur`, 
                `taille_joueur`, 
                `poids_joueur`, 
                `dte_naissance`, 
                `GP`, 
                `MIN`, 
                `FGM`, 
                `FGA`, 
                `3PM`, 
                `3PA`, 
                `FTM`, 
                `FTA`, 
                `TOV`, 
                `PF`, 
                `ORB`, 
                `DRB`, 
                `REB`, 
                `AST`, 
                `STL`, 
                `BLK`, 
                `PTS`,
                `MIN_dernier_match`, 
                `FGM_dernier_match`, 
                `FGA_dernier_match`, 
                `3PM_dernier_match`, 
                `3PA_dernier_match`,
                `FTM_dernier_match`, 
                `FTA_dernier_match`, 
                `TOV_dernier_match`, 
                `PF_dernier_match`, 
                `ORB_dernier_match`, 
                `DRB_dernier_match`,
                `REB_dernier_match`, 
                `AST_dernier_match`, 
                `STL_dernier_match`, 
                `BLK_dernier_match`, 
                `PTS_dernier_match`,
                `NB_match_bons_accumules`, 
                `Probas_1_3`, 
                `Probas_4_5`, 
                `Probas_5_10`, 
                `Probas_11_15`, 
                `Probas_16_20`, 
                `Probas_21_30`, 
                `Probas_31_35`, 
                `Probas_36_40`, 
                `Probas_Nul`, 
                `Probas_Pas_Fou`, 
                `Probas_Moyen`, 
                `Probas_Bien`, 
                `Probas_Exellent`
            ) VALUES (
                :id_utilisateur, 
                :nom_joueur, 
                'Homme', 
                180, 
                75, 
                '2000-01-01', 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0.4, 
                0.25, 
                0.15, 
                0.1, 
                0.04, 
                0.03, 
                0.02, 
                0.01, 
                0.4, 
                0.25, 
                0.15, 
                0.15, 
                0.05  
            )
            ");

            $query->bindParam(':id_utilisateur', $_SESSION['user_id'], PDO::PARAM_INT);
            $query->bindParam(':nom_joueur', $nom_joueur, PDO::PARAM_STR);
            $query->execute();

            
            
        } catch (Exception $e) {
            // Catch any errors and return the error message
            return "Error: " . $e->getMessage();
        }
    }
    
    
    

    function Calculer_Rebonds($niveau, $taille_joueur){
    $reb_def = 0;
    $reb_off = 0;

        if ($niveau == 'nul') {
            if ($taille_joueur <= 170) {
                $reb_def = random_int(0, 2);
                $reb_off = random_int(0, 1);
            } elseif ($taille_joueur <= 180) {
                $reb_def = random_int(0, 3);
                $reb_off = random_int(0, 1);
            } elseif ($taille_joueur <= 190) {
                $reb_def = random_int(0, 5);
                $reb_off = random_int(0, 3);
            } elseif ($taille_joueur <= 200) {
                $reb_def = random_int(0, 5);
                $reb_off = random_int(0, 5);
            } elseif ($taille_joueur <= 210) {
                $reb_def = random_int(0, 6);
                $reb_off = random_int(0, 6);
            }
        } elseif ($niveau == 'Pas Fou') {
            if ($taille_joueur <= 170) {
                $reb_def = random_int(0, 2);
                $reb_off = random_int(0, 2);
            } elseif ($taille_joueur <= 180) {
                $reb_def = random_int(0, 3);
                $reb_off = random_int(0, 2);
            } elseif ($taille_joueur <= 190) {
                $reb_def = random_int(1, 5);
                $reb_off = random_int(0, 3);
            } elseif ($taille_joueur <= 200) {
                $reb_def = random_int(1, 5);
                $reb_off = random_int(0, 5);
            } elseif ($taille_joueur <= 210) {
                $reb_def = random_int(0, 6);
                $reb_off = random_int(1, 6);
            }
        } elseif ($niveau == 'Moyen') {
            if ($taille_joueur <= 170) {
                $reb_def = random_int(0, 3);
                $reb_off = random_int(0, 2);
            } elseif ($taille_joueur <= 180) {
                $reb_def = random_int(1, 3);
                $reb_off = random_int(0, 2);
            } elseif ($taille_joueur <= 190) {
                $reb_def = random_int(2, 5);
                $reb_off = random_int(1, 2);
            } elseif ($taille_joueur <= 200) {
                $reb_def = random_int(1, 5);
                $reb_off = random_int(2, 5);
            } elseif ($taille_joueur <= 210) {
                $reb_def = random_int(1, 6);
                $reb_off = random_int(1, 6);
            }
        } elseif ($niveau == 'Bien') {
            if ($taille_joueur <= 170) {
                $reb_def = random_int(1, 3);
                $reb_off = random_int(1, 2);
            } elseif ($taille_joueur <= 180) {
                $reb_def = random_int(2, 3);
                $reb_off = random_int(2, 4);
            } elseif ($taille_joueur <= 190) {
                $reb_def = random_int(3, 6);
                $reb_off = random_int(2, 7);
            } elseif ($taille_joueur <= 200) {
                $reb_def = random_int(3, 9);
                $reb_off = random_int(3, 7);
            } elseif ($taille_joueur <= 210) {
                $reb_def = random_int(4, 9);
                $reb_off = random_int(5, 9);
            }
        } elseif ($niveau == 'Exellent') {
            if ($taille_joueur <= 170) {
                $reb_def = random_int(2, 4);
                $reb_off = random_int(2, 5);
            } elseif ($taille_joueur <= 180) {
                $reb_def = random_int(3, 5);
                $reb_off = random_int(2, 6);
            } elseif ($taille_joueur <= 190) {
                $reb_def = random_int(4, 6);
                $reb_off = random_int(3, 6);
            } elseif ($taille_joueur <= 200) {
                $reb_def = random_int(5, 9);
                $reb_off = random_int(4, 7);
            } elseif ($taille_joueur <= 210) {
                $reb_def = random_int(6, 8);
                $reb_off = random_int(7, 10);
            }
        }

        return [$reb_def, $reb_off];
    }
    
    
    

    

    function Change_Proba($proba_ranges, $niveau, $tps_jeu, $panier_2_points, $pts_2_tentes, $panier_3_points, $pts_3_tentes, $panier_lancers_francs, $LF_tente, $turnovers_nb, $foul_nb, $reb_off, $reb_def, $nb_rebonds, $assist_nb, $steal_nb, $block_nb, $nbpointstot, $id_utilisateur, $nom_joueur_envoyer) {
    
        global $bdd;
    
        if (!$bdd) {
            echo 'Échec de la connexion à la base de données';
        } else {
            echo 'Connexion réussie à la base de données';
        }
        
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
        $query = $bdd->prepare("
            SELECT NB_match_bons_accumules 
            FROM mycareer
            WHERE id_utilisateur = :id_utilisateur
            AND nom_joueur = :nom_joueur
        ");
        
        $query->bindParam(':id_utilisateur', $id_utilisateur);
        $query->bindParam(':nom_joueur', $nom_joueur_envoyer);
        $query->execute();
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $NB_match_accumulés = $result['NB_match_bons_accumules'] + 1;
    
        $Proba_Pas_Bon = $proba_ranges['Moyen'] + $proba_ranges['Nul'] + $proba_ranges['Pas Fou'];
        $Proba_Bon = 1 - $Proba_Pas_Bon;
    
        if ($niveau == 'Nul' || $niveau == 'Pas Fou' || $niveau == 'Moyen') {
            if ($Proba_Pas_Bon > $Proba_Bon) {
                $nul = $result['Nul'] + 0.1;
                $pas_fou = $result['Pas Fou'] - 0.1;
                $moyen = $result['Moyen'] - 0.07;
                $bien = $result['Bien'] + 0.15;
                $exellent = min($result['Exellent'] + 0.05, 0.60);
            } else {
                $nul = $result['Nul'] + 0.2;
                $pas_fou = $result['Pas Fou'] + 0.15;
                $moyen = $result['Moyen'] + 0.1;
                $bien = $result['Bien'] - 0.2;
                $exellent = min($result['Exellent'] - 0.2, 0.60);
            }
        } elseif ($niveau == 'Bien' || $niveau == 'Exellent') {
            if ($Proba_Pas_Bon > $Proba_Bon) {
                $nul = $result['Nul'] + 0.30;
                $pas_fou = $result['Pas Fou'] + 0.1;
                $moyen = $result['Moyen'] + 0.07;
                $bien = $result['Bien'] - 0.15;
                $exellent = min($result['Exellent'] - 0.30, 0.60);
            } else {
                $nul = $result['Nul'] + 0.2;
                $pas_fou = $result['Pas Fou'] - 0.01;
                $moyen = $result['Moyen'] - 0.01;
                $bien = $result['Bien'] + 0.01;
                $exellent = min($result['Exellent'] + 0.03, 0.60);
            }
        }
    
        if ($NB_match_accumulés >= 3) {
            $nul += random_int(30, 70) / 100;
            $pasFou += random_int(15, 25) / 100;
            $moyen -= random_int(5, 15) / 100;
            $bien -= random_int(10, 15) / 100;
            $exellent = min($exellent - random_int(11, 40) / 100, 0.60);
            
            $NB_match_accumulés = 0;
        }
    
        $proba_ranges2 = [
            'Nul' => $nul,  
            'Pas Fou' => $nul + $pasFou,  
            'Moyen' => $nul + $pasFou + $moyen, 
            'Bien' => $nul + $pasFou + $moyen + $bien,  
            'Exellent' => $nul + $pasFou + $moyen + $bien +  $exellent

        ];
        

            // Récupération des valeurs de probabilité existantes
        $range_1_3 = $result['1_3'];
        $range_4_5 = $result['4_5'];
        $range_5_10 = $result['5_10'];
        $range_11_15 = $result['11_15'];
        $range_16_20 = $result['16_20'];
        $range_21_30 = $result['21_30'];
        $range_31_35 = $result['31_35'];
        $range_36_40 = $result['36_40'];

        // Condition : moins de 20 minutes -> niveau faible
        if ($tps_jeu < 20) {
            if ($Proba_Pas_Bon > $Proba_Bon) {
                $range_1_3 -= 0.05;
                $range_4_5 -= 0.05;
                $range_5_10 -= 0.04;
                $range_11_15 += 0.08;
                $range_16_20 = min($range_16_20 + 0.06, 0.60);
            } else {
                $range_1_3 += 0.1;
                $range_4_5 += 0.08;
                $range_5_10 += 0.06;
                $range_11_15 -= 0.08;
                $range_16_20 = min($range_16_20 - 0.05, 0.60);
            }
        } 
        // Condition : temps de jeu de 20 minutes ou plus -> niveau élevé
        else {
            if ($Proba_Pas_Bon > $Proba_Bon) {
                $range_1_3 += 0.2;
                $range_4_5 += 0.1;
                $range_5_10 += 0.05;
                $range_11_15 -= 0.1;
                $range_16_20 = min($range_16_20 - 0.08, 0.60);
            } else {
                $range_1_3 -= 0.005;
                $range_4_5 -= 0.005;
                $range_5_10 -= 0.005;
                $range_11_15 += 0.015;
                $range_16_20 = min($range_16_20 + 0.02, 0.60);
            }
        }

        

        $proba_ranges = [
            '1_3' => $range_1_3,  // Valeur ajustée
            '4_5' => $range_1_3 + $range_4_5,  // Cumul des intervalles
            '5_10' => $range_1_3 + $range_4_5 + $range_5_10,  // Cumul
            '11_15' => $range_1_3 + $range_4_5 + $range_5_10 + $range_11_15,  // Cumul
            '16_20' => $range_1_3 + $range_4_5 + $range_5_10 + $range_11_15 + $range_16_20,  // Cumul
            '21_30' => $range_1_3 + $range_4_5 + $range_5_10 + $range_11_15 + $range_16_20 + $range_21_30,  // Cumul
            '31_35' => $range_1_3 + $range_4_5 + $range_5_10 + $range_11_15 + $range_16_20 + $range_21_30 + $range_31_35,  // Cumul
            '36_40' => $range_1_3 + $range_4_5 + $range_5_10 + $range_11_15 + $range_16_20 + $range_21_30 + $range_31_35 + $range_36_40
        ];

        print_r($proba_ranges);
        

        // Assurez-vous que les probabilités ne deviennent pas négatives
        foreach ($proba_ranges2 as $key => $value) {
            if ($value < 0) {
                $proba_ranges2[$key] = 0; // Remplacer les valeurs négatives par 0
            }
        }

        foreach ($proba_ranges as $key => $value) {
            if ($value < 0) {
                $proba_ranges[$key] = 0; // Remplacer les valeurs négatives par 0
            }
        }

        // Fonction de normalisation
        function normalize(&$probabilities) {
            $total = array_sum($probabilities);
            if ($total > 0) {
                foreach ($probabilities as $key => $value) {
                    // On assure que chaque probabilité est entre 0 et 1
                    $probabilities[$key] = max(0, min(1, $value / $total));
                }
            } else {
                // Si le total est 0, renvoyer des probabilités par défaut équilibrées
                $nb_elements = count($probabilities);
                foreach ($probabilities as $key => $value) {
                    $probabilities[$key] = 1 / $nb_elements;
                }
            }
        }
        
        // Normaliser les probabilités
        normalize($proba_ranges2);
        normalize($proba_ranges);
        
        
        $query = $bdd->prepare("
            SELECT GP 
            FROM mycareer
            WHERE id_utilisateur = :id_utilisateur
            AND nom_joueur = :nom_joueur
        ");
        
        $query->bindParam(':id_utilisateur', $id_utilisateur);
        $query->bindParam(':nom_joueur', $nom_joueur_envoyer);
        $query->execute();


        $result = $query->fetch(PDO::FETCH_ASSOC);
        $GP = $result['GP'] + 1;
        echo $GP;
        echo ('---------------------------------');
        
        // Mise à jour des stats et des probabilités dans la base de données
        $query = $bdd->prepare("
        UPDATE mycareer
        SET 
            GP = :GP,
            `MIN` = :tps_jeu,
            FGM = :panier_2_points,
            FGA = :pts_2_tentes,
            `3PM` = :panier_3_points,
            `3PA` = :pts_3_tentes,
            FTM = :panier_lancers_francs,
            FTA = :LF_tente,
            TOV = :turnovers_nb,
            PF = :foul_nb,
            ORB = :reb_off,
            DRB = :reb_def,
            REB = :nb_rebonds,
            AST = :assist_nb,
            STL = :steal_nb,
            BLK = :block_nb,
            PTS = :nbpointstot,
            `MIN_dernier_match` = :tps_jeu,
            FGM_dernier_match = :panier_2_points,
            FGA_dernier_match = :pts_2_tentes,
            `3PM_dernier_match` = :panier_3_points,
            `3PA_dernier_match` = :pts_3_tentes,
            FTM_dernier_match = :panier_lancers_francs,
            FTA_dernier_match = :LF_tente,
            TOV_dernier_match = :turnovers_nb,
            PF_dernier_match = :foul_nb,
            ORB_dernier_match = :reb_off,
            DRB_dernier_match = :reb_def,
            REB_dernier_match = :nb_rebonds,
            AST_dernier_match = :assist_nb,
            STL_dernier_match = :steal_nb,
            BLK_dernier_match = :block_nb,
            PTS_dernier_match = :nbpointstot,
            Probas_1_3 = :Probas_1_3,
            Probas_4_5 = :Probas_4_5,
            Probas_5_10 = :Probas_5_10,
            Probas_11_15 = :Probas_11_15,
            Probas_16_20 = :Probas_16_20,
            Probas_21_30 = :Probas_21_30,
            Probas_31_35 = :Probas_31_35,
            Probas_36_40 = :Probas_36_40,
            Probas_Nul = :Probas_Nul,
            Probas_Pas_Fou = :Probas_Pas_Fou,
            Probas_Moyen = :Probas_Moyen,
            Probas_Bien = :Probas_Bien,
            Probas_Exellent = :Probas_Exellent,
            NB_match_bons_accumules = :NB_match_bons_accumules
        WHERE 
            id_utilisateur = :id_utilisateur AND 
            nom_joueur = :nom_joueur
        ");



        
        // Lier les paramètres
        $query->bindParam(':id_utilisateur', $id_utilisateur);
        $query->bindParam(':nom_joueur', $nom_joueur_envoyer);
        $query->bindParam(':GP', $GP);
        $query->bindParam(':tps_jeu', $tps_jeu);
        $query->bindParam(':panier_2_points', $panier_2_points);
        $query->bindParam(':pts_2_tentes', $pts_2_tentes);
        $query->bindParam(':panier_3_points', $panier_3_points);
        $query->bindParam(':pts_3_tentes', $pts_3_tentes);
        $query->bindParam(':panier_lancers_francs', $panier_lancers_francs);
        $query->bindParam(':LF_tente', $LF_tente);
        $query->bindParam(':turnovers_nb', $turnovers_nb);
        $query->bindParam(':foul_nb', $foul_nb);
        $query->bindParam(':reb_off', $reb_off);
        $query->bindParam(':reb_def', $reb_def);
        $query->bindParam(':nb_rebonds', $nb_rebonds);  // Corrigé ici pour correspondre au bon paramètre
        $query->bindParam(':assist_nb', $assist_nb);
        $query->bindParam(':steal_nb', $steal_nb);
        $query->bindParam(':block_nb', $block_nb);
        $query->bindParam(':nbpointstot', $nbpointstot);
        $query->bindParam(':Probas_1_3', $proba_ranges['1_3']);
        $query->bindParam(':Probas_4_5', $proba_ranges['4_5']);
        $query->bindParam(':Probas_5_10', $proba_ranges['5_10']);
        $query->bindParam(':Probas_11_15', $proba_ranges['11_15']);
        $query->bindParam(':Probas_16_20', $proba_ranges['16_20']);
        $query->bindParam(':Probas_21_30', $proba_ranges['21_30']);
        $query->bindParam(':Probas_31_35', $proba_ranges['31_35']);
        $query->bindParam(':Probas_36_40', $proba_ranges['36_40']);
        $query->bindParam(':Probas_Nul', $proba_ranges2['Nul']);
        $query->bindParam(':Probas_Pas_Fou', $proba_ranges2['Pas Fou']);
        $query->bindParam(':Probas_Moyen', $proba_ranges2['Moyen']);
        $query->bindParam(':Probas_Bien', $proba_ranges2['Bien']);
        $query->bindParam(':Probas_Exellent', $proba_ranges2['Exellent']);
        $query->bindParam(':NB_match_bons_accumules', $NB_match_accumulés);
        
    
    
        // Exécuter la requête et afficher les erreurs
        try {
            if (!$query->execute()) {
                $error = $query->errorInfo();
                echo "Erreur SQL : " . $error[2];
            } else {
                echo "Mise à jour réussie.";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    
        echo 'juste apres';
    }
    

    ?>