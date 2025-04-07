<?php
session_start();
include('menu.php');
include('db.php'); // Connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "Vous devez être connecté pour accéder à cette page.";
    exit;
}
$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Récupérer les systèmes découverts par l'utilisateur
$sql_systemes = "
    SELECT s.id, s.nom, s.coord_x, s.coord_y, s.coord_z
    FROM utilisateur.systeme_decouvert sd
    JOIN jeu.systeme s ON sd.systeme_id = s.id
    WHERE sd.utilisateur_id = ?
    ORDER BY sd.id ASC";
$stmt_systemes = $conn->prepare($sql_systemes);
$stmt_systemes->bind_param("i", $user_id);
$stmt_systemes->execute();
$result_systemes = $stmt_systemes->get_result();
$systemes = $result_systemes->fetch_all(MYSQLI_ASSOC);

// Identifier le système d'origine
$systeme_origine = $systemes[0];
$origine_x = $systeme_origine['coord_x'];
$origine_y = $systeme_origine['coord_y'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planètes</title>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <style>
        .map-container {
            width: 100%;
            height: 600px;
        }
        .systeme {
            fill: yellow;
            stroke: black;
            stroke-width: 1px;
        }
        .planete {
            fill: green;
        }
        .label {
            font-size: 12px;
            fill: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vos planètes</h1>
        <p>Voici la liste des systèmes et des planètes que vous avez découverts :</p>
        <div id="systemes-planetes">
            <ul>
                <?php foreach ($systemes as $systeme): ?>
                    <li>Système: <?php echo htmlspecialchars($systeme['nom']); ?></li>
                    <?php
                    // Récupérer les planètes du système
                    $sql_planetes = "
                        SELECT p.nom, p.coord_x, p.coord_y
                        FROM jeu.planete p
                        WHERE p.systeme_id = ?";
                    $stmt_planetes = $conn->prepare($sql_planetes);
                    $stmt_planetes->bind_param("i", $systeme['id']);
                    $stmt_planetes->execute();
                    $result_planetes = $stmt_planetes->get_result();
                    $planetes = $result_planetes->fetch_all(MYSQLI_ASSOC);
                    ?>
                    <?php if (count($planetes) > 0): ?>
                        <ul>
                            <?php foreach ($planetes as $planete): ?>
                                <li>Planète: <?php echo htmlspecialchars($planete['nom']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <li>Aucune planète trouvée dans ce système.</li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <div id="map" class="map-container"></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const width = document.getElementById('map').clientWidth;
            const height = document.getElementById('map').clientHeight;
            const centerX = width / 2;
            const centerY = height / 2;
            const originX = <?php echo $origine_x; ?>;
            const originY = <?php echo $origine_y; ?>;
            const offsetX = centerX - originX;
            const offsetY = centerY - originY;

            const svg = d3.select('#map').append('svg')
                .attr('width', width)
                .attr('height', height);

            const systemes = <?php echo json_encode($systemes); ?>;

            systemes.forEach(systeme => {
                const systemeX = systeme.coord_x + offsetX;
                const systemeY = systeme.coord_y + offsetY;

                svg.append('circle')
                    .attr('class', 'systeme')
                    .attr('cx', systemeX)
                    .attr('cy', systemeY)
                    .attr('r', 5)
                    .append('title')
                    .text(systeme.nom);

                const planetes = <?php
                $planetes = [];
                foreach ($systemes as $sys) {
                    $sql_planetes = "
                        SELECT p.nom, p.coord_x, p.coord_y
                        FROM jeu.planete p
                        WHERE p.systeme_id = " . $sys['id'];
                    $result_planetes = $conn->query($sql_planetes);
                    while ($row = $result_planetes->fetch_assoc()) {
                        $row['systeme_coord_x'] = $sys['coord_x'];
                        $row['systeme_coord_y'] = $sys['coord_y'];
                        $planetes[] = $row;
                    }
                }
                echo json_encode($planetes);
                ?>;

                planetes.forEach(planete => {
                    if (planete.systeme_coord_x == systeme.coord_x && planete.systeme_coord_y == systeme.coord_y) {
                        const planeteX = systeme.coord_x + parseInt(planete.coord_x);
                        const planeteY = systeme.coord_y + parseInt(planete.coord_y);

                        svg.append('circle')
                            .attr('class', 'planete')
                            .attr('cx', planeteX + offsetX)
                            .attr('cy', planeteY + offsetY)
                            .attr('r', 3)
                            .append('title')
                            .text(planete.nom);
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
$stmt_systemes->close();
$conn->close();
?>