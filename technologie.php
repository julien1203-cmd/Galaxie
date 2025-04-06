<?php
session_start();
include('menu.php');
include('db.php'); // Connexion à la base de données

// Récupération des technologies disponibles
$sql = "SELECT * FROM technologie";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technologies</title>
</head>
<body>
    <div class="container">
        <h1>Arbre des technologies</h1>
        <p>Découvrez les différentes technologies que vous pouvez rechercher et débloquer dans le jeu.</p>
        
        <?php
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Nom de la technologie</th><th>Description</th><th>Coût</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['nom'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['cout_recherche'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Aucune technologie disponible pour l'instant.</p>";
        }
        ?>
    </div>
</body>
</html>
