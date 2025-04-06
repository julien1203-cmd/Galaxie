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

// Vérifier si un ID de planète est fourni dans l'URL
$planete_id = isset($_GET['planete_id']) ? (int)$_GET['planete_id'] : 1;

// Vérifier si la planète appartient à l'utilisateur
$sql_planete = "SELECT * FROM planete WHERE id = ? AND utilisateur_id = ?";
$stmt_planete = $conn->prepare($sql_planete);
if (!$stmt_planete) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_planete->bind_param("ii", $planete_id, $user_id);
$stmt_planete->execute();
$planete_result = $stmt_planete->get_result();
$planete = $planete_result->fetch_assoc();

if (!$planete) {
    echo "Vous n'avez pas la permission d'ajouter des bâtiments à cette planète.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ajouter un nouveau bâtiment
    $nom = $_POST['nom'];
    $niveau = $_POST['niveau'];
    
    $sql = "INSERT INTO batiment (nom, niveau, planete_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("sii", $nom, $niveau, $planete_id);
    $stmt->execute();
    $stmt->close();

    echo "<p>Bâtiment ajouté avec succès!</p>";
    header("Location: batiments.php?planete_id=" . $planete_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un bâtiment</title>
</head>
<body>
    <div class="container">
        <h1>Ajouter un nouveau bâtiment à la planète #<?php echo $planete_id; ?></h1>

        <form method="POST">
            <label for="nom">Nom du bâtiment :</label>
            <input type="text" name="nom" id="nom" required>
            <label for="niveau">Niveau :</label>
            <input type="number" name="niveau" id="niveau" min="1" required>
            <button type="submit">Ajouter le bâtiment</button>
        </form>

        <p><a href="batiments.php?planete_id=<?php echo $planete_id; ?>">Retour aux bâtiments</a></p>
    </div>
</body>
</html>

<?php
$conn->close();
?>