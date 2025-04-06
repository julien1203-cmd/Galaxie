<?php
//include('menu.php');
include('db2.php'); // Connexion à la base de données

// Vérifier si l'utilisateur est connecté
//session_start();
if (!isset($_SESSION['user_id'])) {
    echo "Vous devez être connecté pour accéder à cette page.";
    exit;
}
$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Vérifier si un ID de bâtiment est fourni dans l'URL
$batiment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$batiment = null;

if ($batiment_id > 0) {
    // Récupérer les informations du bâtiment
    $sql = "SELECT * FROM batiment WHERE id = ? AND EXISTS (SELECT 1 FROM planete WHERE id = batiment.planete_id AND utilisateur_id = ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("ii", $batiment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $batiment = $result->fetch_assoc();

    if ($batiment) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Mettre à jour le niveau du bâtiment
            $nouveau_niveau = $_POST['niveau'];
            $update_sql = "UPDATE batiment SET niveau = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            if (!$update_stmt) {
                die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
            }
            $update_stmt->bind_param("ii", $nouveau_niveau, $batiment_id);
            $update_stmt->execute();
            $update_stmt->close();

            echo "<p>Bâtiment mis à jour avec succès!</p>";
            header("Location: batiments.php?planete_id=" . $batiment['planete_id']);
            exit;
        }
    } else {
        echo "<p>Bâtiment introuvable ou vous n'êtes pas autorisé à modifier ce bâtiment.</p>";
    }
} else {
    echo "<p>ID de bâtiment invalide.</p>";
}

$stmt->close();
$result->free();  // Ajouter la libération du résultat ici
$conn->close();
?>