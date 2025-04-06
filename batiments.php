<?php
session_start();  // Démarrer la session
include('menu.php');
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "Vous devez être connecté pour accéder à cette page.";
    exit;
}

// Connexion à la base de données
include('db2.php');  // Inclure le fichier de connexion à la base de données

// Récupérer l'ID de l'utilisateur depuis la session
$user_id = $_SESSION['user_id'];

// Récupérer les bâtiments associés à l'utilisateur et à ses planètes
$sql = "SELECT b.id, b.nom, b.niveau, b.planete_id
        FROM batiment b
        JOIN planete p ON b.planete_id = p.id
        WHERE p.utilisateur_id = ?";  // Assurer que la planète appartient à l'utilisateur
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);  // Lier l'ID de l'utilisateur à la requête
$stmt->execute();
$result = $stmt->get_result();

// Vérifier si des bâtiments existent pour l'utilisateur
if ($result->num_rows > 0) {
    echo "<h2>Vos bâtiments</h2>";
    echo "<table>";
    echo "<tr><th>Nom</th><th>Niveau</th><th>Planète</th></tr>";

    while ($row = $result->fetch_assoc()) {
        // Afficher chaque bâtiment
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
        echo "<td>" . $row['niveau'] . "</td>";
        
        // Récupérer le nom de la planète associée à ce bâtiment
        $planete_id = $row['planete_id'];
        $sql_planete = "SELECT nom FROM planete WHERE id = ?";
        $stmt_planete = $conn->prepare($sql_planete);
        $stmt_planete->bind_param("i", $planete_id);
        $stmt_planete->execute();
        $result_planete = $stmt_planete->get_result();
        $planete = $result_planete->fetch_assoc();
        echo "<td>" . htmlspecialchars($planete['nom']) . "</td>";
        
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Aucun bâtiment trouvé.";
}

// Fermer la requête préparée et la connexion à la base de données
$stmt->close();
$conn->close();
include('modifier_batiment.php');
include('ajouter_batiment.php');
?>
