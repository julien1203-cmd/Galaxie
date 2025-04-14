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

// Vérifier si un ID de bâtiment est fourni dans le formulaire
if (!isset($_POST['batiment_id'])) {
    echo "Aucun bâtiment spécifié.";
    exit;
}
$batiment_id = (int)$_POST['batiment_id'];

// Mettre à jour le niveau du bâtiment pour l'utilisateur
$sql_update_niveau = "UPDATE utilisateur.utilisateur_batiments 
                      SET niveau = niveau + 1, temps_fin = NULL 
                      WHERE utilisateur_id = ? AND batiment_id = ?";
$stmt_update_niveau = $conn->prepare($sql_update_niveau);
if ($stmt_update_niveau === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_update_niveau->bind_param("ii", $user_id, $batiment_id);
$stmt_update_niveau->execute();

if ($stmt_update_niveau->affected_rows > 0) {
    echo "<p>Bâtiment débloqué avec succès !</p>";
} else {
    echo "<p>Erreur lors du déblocage du bâtiment.</p>";
}

echo "<p><a href='batiments.php'>Retour à la gestion des bâtiments</a></p>";

$stmt_update_niveau->close();
$conn->close();
?>