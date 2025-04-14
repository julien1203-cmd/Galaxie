<?php
include('menu.php');
include('db.php'); // Connexion à la base de données

// Vérifier si l'utilisateur est connecté
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "Vous devez être connecté pour accéder à cette page.";
    exit;
}
$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Vérifier si un ID de technologie est fourni dans l'URL
$technologie_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Vérifier si l'utilisateur a une recherche en cours
$sql_recherche = "SELECT * FROM recherche WHERE utilisateur_id = ?";
$stmt_recherche = $conn->prepare($sql_recherche);
if ($stmt_recherche === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_recherche->bind_param("i", $user_id);
$stmt_recherche->execute();
$result_recherche = $stmt_recherche->get_result();
$recherche_en_cours = $result_recherche->fetch_assoc();

if ($recherche_en_cours) {
    echo "Vous avez déjà une recherche en cours. Veuillez attendre qu'elle soit terminée avant d'en lancer une nouvelle.";
    exit;
}

// Récupérer les informations de la technologie
$sql_technologie = "SELECT * FROM technologie WHERE id = ?";
$stmt_technologie = $conn->prepare($sql_technologie);
if ($stmt_technologie === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_technologie->bind_param("i", $technologie_id);
$stmt_technologie->execute();
$result_technologie = $stmt_technologie->get_result();
$technologie = $result_technologie->fetch_assoc();

if (!$technologie) {
    echo "Technologie introuvable.";
    exit;
}

// Récupérer le niveau du laboratoire de recherche de l'utilisateur
$sql_laboratoire = "SELECT niveau FROM utilisateur.laboratoire WHERE utilisateur_id = ?";
$stmt_laboratoire = $conn->prepare($sql_laboratoire);
if ($stmt_laboratoire === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_laboratoire->bind_param("i", $user_id);
$stmt_laboratoire->execute();
$result_laboratoire = $stmt_laboratoire->get_result();
$laboratoire = $result_laboratoire->fetch_assoc();
$niveau_laboratoire = $laboratoire['niveau'];

// Calculer le temps de développement en secondes
$temps_developpement = ($technologie['temps_base'] / $niveau_laboratoire) * 3600;

// Calculer le temps de fin
$temps_fin = date('Y-m-d H:i:s', time() + $temps_developpement);

// Insérer la recherche dans la table des recherches en cours
$sql_insert_recherche = "INSERT INTO recherche (utilisateur_id, technologie_id, temps_restant, temps_fin) VALUES (?, ?, ?, ?)";
$stmt_insert_recherche = $conn->prepare($sql_insert_recherche);
if ($stmt_insert_recherche === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_insert_recherche->bind_param("iiis", $user_id, $technologie_id, $temps_developpement, $temps_fin);
$stmt_insert_recherche->execute();

echo "<p>Recherche de la technologie '" . htmlspecialchars($technologie['nom']) . "' lancée avec succès !</p>";
echo "<p>Temps de développement : " . gmdate('H:i:s', $temps_developpement) . " (heures:minutes:secondes).</p>";

$stmt_technologie->close();
$stmt_laboratoire->close();
$stmt_insert_recherche->close();
$stmt_recherche->close();
$conn->close();
?>