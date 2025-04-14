<?php
session_start();
include('menu.php');
include('db3.php'); // Connexion à la base de données jeu
include('db4.php'); // Connexion à la base de données utilisateur

// Vérifiez la connexion à la base utilisateur
if (!$conn_utilisateur) {
    die("La connexion à la base de données utilisateur a échoué.");
}

// Vérifiez la connexion à la base jeu
if (!$conn_jeu) {
    die("La connexion à la base de données jeu a échoué.");
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "Vous devez être connecté pour accéder à cette page.";
    exit;
}

// Vérifier que l'utilisateur arrive bien via le bouton "Voir les résultats"
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['observation_complete']) || $_POST['observation_complete'] != 1) {
    echo "Accès non autorisé.";
    exit;
}

$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Récupérer le système actuel de l'utilisateur
$sql_user_position = "
    SELECT p.systeme_id 
    FROM utilisateur.planete p
    WHERE p.utilisateur_id = ?";
$stmt_user_position = $conn_utilisateur->prepare($sql_user_position);
if (!$stmt_user_position) {
    die("Erreur dans la préparation de la requête : " . $conn_utilisateur->error);
}
$stmt_user_position->bind_param("i", $user_id);
$stmt_user_position->execute();
$result_user_position = $stmt_user_position->get_result();
$user_system = $result_user_position->fetch_assoc();

if (!$user_system) {
    echo "Impossible de récupérer votre position actuelle.";
    exit;
}
$user_system_id = $user_system['systeme_id'];

// Récupérer les coordonnées du système de l'utilisateur
$sql_user_system_coords = "
    SELECT coord_x, coord_y, coord_z 
    FROM jeu.systeme 
    WHERE id = ?";
$stmt_user_system_coords = $conn_jeu->prepare($sql_user_system_coords);
$stmt_user_system_coords->bind_param("i", $user_system_id);
$stmt_user_system_coords->execute();
$result_user_system_coords = $stmt_user_system_coords->get_result();
$user_coords = $result_user_system_coords->fetch_assoc();

if (!$user_coords) {
    echo "Impossible de récupérer les coordonnées de votre système.";
    exit;
}
$user_x = $user_coords['coord_x'];
$user_y = $user_coords['coord_y'];
$user_z = $user_coords['coord_z'];

// Récupérer tous les systèmes dans la base
$sql_all_systems = "
    SELECT id, coord_x, coord_y, coord_z 
    FROM jeu.systeme";
$result_all_systems = $conn_jeu->query($sql_all_systems);

if (!$result_all_systems) {
    echo "Erreur lors de la récupération des systèmes.";
    exit;
}

// Récupérer les systèmes déjà découverts par l'utilisateur
$sql_discovered_systems = "
    SELECT systeme_id 
    FROM utilisateur.systeme_decouvert 
    WHERE utilisateur_id = ?";
$stmt_discovered_systems = $conn_utilisateur->prepare($sql_discovered_systems);
$stmt_discovered_systems->bind_param("i", $user_id);
$stmt_discovered_systems->execute();
$result_discovered_systems = $stmt_discovered_systems->get_result();

$discovered_systems = [];
while ($row = $result_discovered_systems->fetch_assoc()) {
    $discovered_systems[] = $row['systeme_id'];
}

// Calculer les distances et détecter de nouveaux systèmes
$newly_discovered = [];
$observatoire_niveau = 1; // Exemple : niveau 1 de l'observatoire
$max_systems = $observatoire_niveau * 10; // Nombre maximum de systèmes à détecter

while ($system = $result_all_systems->fetch_assoc()) {
    $system_id = $system['id'];

    // Ignorer les systèmes déjà découverts par l'utilisateur
    if (in_array($system_id, $discovered_systems)) {
        continue;
    }

    // Calculer la distance entre le système de l'utilisateur et le système actuel
    $distance = sqrt(
        pow($user_x - $system['coord_x'], 2) +
        pow($user_y - $system['coord_y'], 2) +
        pow($user_z - $system['coord_z'], 2)
    );

    // Probabilité de détection : plus la distance est grande, moins le système a de chances d'être détecté
    $detection_chance = max(100 - ($distance / 10), 1); // Exemple : 100% - (distance / 10)
    if (rand(1, 100) <= $detection_chance) {
        $newly_discovered[] = $system_id;

        // Arrêter si le maximum de systèmes détectés est atteint
        if (count($newly_discovered) >= $max_systems) {
            break;
        }
    }
}

// Enregistrer les nouveaux systèmes détectés dans la base utilisateur
foreach ($newly_discovered as $system_id) {
    $sql_insert_discovered = "
        INSERT INTO utilisateur.systeme_decouvert (utilisateur_id, systeme_id) 
        VALUES (?, ?)";
    $stmt_insert_discovered = $conn_utilisateur->prepare($sql_insert_discovered);
    $stmt_insert_discovered->bind_param("ii", $user_id, $system_id);
    $stmt_insert_discovered->execute();
}

// Afficher les systèmes détectés
if (count($newly_discovered) > 0) {
    echo "<h2>Nouveaux systèmes détectés :</h2>";
    echo "<ul>";
    foreach ($newly_discovered as $system_id) {
        echo "<li>Système ID : $system_id</li>";
    }
    echo "</ul>";
} else {
    echo "Aucun nouveau système détecté.";
}

// Fermer les connexions à la base de données
$stmt_user_position->close();
$stmt_user_system_coords->close();
$stmt_discovered_systems->close();
$conn_utilisateur->close();
$conn_jeu->close();
?>