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

// Vérifier si un ID de technologie est fourni dans le formulaire
if (!isset($_POST['technologie_id'])) {
    echo "Aucune technologie spécifiée.";
    exit;
}
$technologie_id = (int)$_POST['technologie_id'];

// Mettre à jour le niveau de la technologie pour l'utilisateur
$sql_update_niveau = "INSERT INTO utilisateur.technologie_utilisateur (utilisateur_id, technologie_id, niveau) VALUES (?, ?, 1)
                      ON DUPLICATE KEY UPDATE niveau = niveau + 1";
$stmt_update_niveau = $conn->prepare($sql_update_niveau);
if ($stmt_update_niveau === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_update_niveau->bind_param("ii", $user_id, $technologie_id);
$stmt_update_niveau->execute();

// Supprimer la recherche en cours de la table recherche
$sql_delete_recherche = "DELETE FROM recherche WHERE utilisateur_id = ? AND technologie_id = ?";
$stmt_delete_recherche = $conn->prepare($sql_delete_recherche);
if ($stmt_delete_recherche === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_delete_recherche->bind_param("ii", $user_id, $technologie_id);
$stmt_delete_recherche->execute();

echo "<p>Technologie débloquée avec succès !</p>";
echo "<p><a href='recherche.php'>Retour au laboratoire de recherche</a></p>";

$stmt_update_niveau->close();
$stmt_delete_recherche->close();
$conn->close();
?>