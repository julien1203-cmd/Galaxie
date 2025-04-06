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

if ($batiment_id > 0) {
    // Récupérer les informations du bâtiment
    $sql = "SELECT * FROM batiment WHERE id = ? AND EXISTS (SELECT 1 FROM planetes WHERE id = batiment.planete_id AND utilisateur_id = ?)";
    $stmt = $conn->prepare($sql);
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

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le bâtiment</title>
</head>
<body>
    <div class="container">
        <h1>Modifier le bâtiment : <?php echo htmlspecialchars($batiment['nom']); ?></h1>

        <form method="POST">
            <label for="niveau">Nouveau niveau :</label>
            <input type="number" name="niveau" id="niveau" value="<?php echo $batiment['niveau']; ?>" min="1" required>
            <button type="submit">Mettre à jour</button>
        </form>

        <p><a href="batiments.php?planete_id=<?php echo $batiment['planete_id']; ?>">Retour aux bâtiments</a></p>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
