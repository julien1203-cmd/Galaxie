<?php
include('menu.php');
include('db.php'); // Connexion à la base de données

// Fonction pour calculer le temps de construction en fonction du niveau du bâtiment
function calculerTempsConstruction($niveau_batiment, $temps_base) {
    return $temps_base * pow(1.5, $niveau_batiment - 1);
}

// Vérifier si l'utilisateur est connecté
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "Vous devez être connecté pour accéder à cette page.";
    exit;
}
$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Vérifier si un ID de bâtiment est fourni dans l'URL
$batiment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Vérifier si l'utilisateur a une construction en cours
$sql_construction = "SELECT * FROM utilisateur.utilisateur_batiments WHERE utilisateur_id = ? AND temps_fin IS NOT NULL";
$stmt_construction = $conn->prepare($sql_construction);
if ($stmt_construction === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_construction->bind_param("i", $user_id);
$stmt_construction->execute();
$result_construction = $stmt_construction->get_result();
$construction_en_cours = $result_construction->fetch_assoc();

if ($construction_en_cours) {
    echo "Vous avez déjà une construction en cours. Veuillez attendre qu'elle soit terminée avant d'en lancer une nouvelle.";
    exit;
}

// Récupérer les informations du bâtiment
$sql_batiment = "SELECT * FROM utilisateur.batiments WHERE id = ?";
$stmt_batiment = $conn->prepare($sql_batiment);
if ($stmt_batiment === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_batiment->bind_param("i", $batiment_id);
$stmt_batiment->execute();
$result_batiment = $stmt_batiment->get_result();
$batiment = $result_batiment->fetch_assoc();

if (!$batiment) {
    echo "Bâtiment introuvable.";
    exit;
}

// Récupérer le niveau actuel du bâtiment pour l'utilisateur
$sql_niveau = "SELECT niveau FROM utilisateur.utilisateur_batiments WHERE utilisateur_id = ? AND batiment_id = ?";
$stmt_niveau = $conn->prepare($sql_niveau);
if ($stmt_niveau === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_niveau->bind_param("ii", $user_id, $batiment_id);
$stmt_niveau->execute();
$result_niveau = $stmt_niveau->get_result();
$niveau_actuel = $result_niveau->fetch_assoc()['niveau'] ?? 1;

// Calculer le temps de construction en secondes
$temps_construction = calculerTempsConstruction($niveau_actuel, $batiment['temps_construction']) * 3600;

// Calculer le temps de fin
$temps_fin = date('Y-m-d H:i:s', time() + $temps_construction);

// Insérer la construction dans la table des bâtiments en cours
$sql_insert_construction = "INSERT INTO utilisateur.utilisateur_batiments (utilisateur_id, batiment_id, niveau, temps_fin) VALUES (?, ?, ?, ?) 
                            ON DUPLICATE KEY UPDATE niveau = VALUES(niveau), temps_fin = VALUES(temps_fin)";
$stmt_insert_construction = $conn->prepare($sql_insert_construction);
if ($stmt_insert_construction === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_insert_construction->bind_param("iiis", $user_id, $batiment_id, $niveau_actuel, $temps_fin);
$stmt_insert_construction->execute();

echo "<p>Construction du bâtiment '" . htmlspecialchars($batiment['nom']) . "' lancée avec succès !</p>";
echo "<p>Temps de construction : " . gmdate('H:i:s', $temps_construction) . " (heures:minutes:secondes).</p>";

$stmt_batiment->close();
$stmt_niveau->close();
$stmt_insert_construction->close();
$stmt_construction->close();
$conn->close();
?>