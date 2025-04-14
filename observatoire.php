<?php
session_start();
include('menu.php');
include('db.php'); // Connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "Vous devez être connecté pour accéder à cette page.";
    exit;
}
$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Vérifier si l'utilisateur possède un observatoire
$sql_observatory = "
    SELECT niveau 
    FROM batiments 
    WHERE utilisateur_id = ? 
    AND nom_batiment = 'Observatoire'";
$stmt_observatory = $conn->prepare($sql_observatory);
$stmt_observatory->bind_param("i", $user_id);
$stmt_observatory->execute();
$result_observatory = $stmt_observatory->get_result();
$observatory = $result_observatory->fetch_assoc();

// Si l'observatoire n'existe pas
if (!$observatory) {
    echo "Vous devez construire un observatoire pour utiliser cette fonctionnalité.";
    exit;
}

$niveau_observatoire = $observatory['niveau'];

// Calculer les chances de découverte en fonction du niveau
$chance_decouverte = $niveau_observatoire * 10; // Par exemple, chaque niveau augmente les chances de 10%

// Lancer une observation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['observer'])) {
    // Simuler une observation avec un délai de 30 minutes
    $temps_observation = 30 * 60; // 30 minutes en secondes
    $heure_fin = time() + $temps_observation;

    // Insérer une observation en cours dans la base de données
    $sql_insert_observation = "
        INSERT INTO observations (utilisateur_id, heure_debut, heure_fin) 
        VALUES (?, NOW(), FROM_UNIXTIME(?))";
    $stmt_insert = $conn->prepare($sql_insert_observation);
    $stmt_insert->bind_param("ii", $user_id, $heure_fin);
    $stmt_insert->execute();

    echo "Observation lancée ! Revenez dans 30 minutes pour voir les résultats.";
    exit;
}

// Vérifier si une observation est en cours
$sql_observation_en_cours = "
    SELECT heure_fin 
    FROM observations 
    WHERE utilisateur_id = ? 
    AND heure_fin > NOW()";
$stmt_observation_en_cours = $conn->prepare($sql_observation_en_cours);
$stmt_observation_en_cours->bind_param("i", $user_id);
$stmt_observation_en_cours->execute();
$result_observation_en_cours = $stmt_observation_en_cours->get_result();
$observation_en_cours = $result_observation_en_cours->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Observatoire</title>
</head>
<body>
    <h1>Observatoire Astronomique</h1>
    <p>Niveau de l'observatoire : <?php echo $niveau_observatoire; ?></p>
    <p>Chances de découverte : <?php echo $chance_decouverte; ?>%</p>

    <?php if ($observation_en_cours): ?>
        <p>Observation en cours. Fin prévue à : <?php echo $observation_en_cours['heure_fin']; ?></p>
    <?php else: ?>
        <form method="POST">
            <button type="submit" name="observer">Lancer une observation</button>
        </form>
    <?php endif; ?>
</body>
</html>
<?php
$stmt_observatory->close();
$stmt_observation_en_cours->close();
$conn->close();
?>