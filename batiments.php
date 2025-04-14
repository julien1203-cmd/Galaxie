<?php
include('menu.php');
include('db.php'); // Connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    echo "Vous devez être connecté pour accéder à cette page.";
    exit;
}
$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Fonction pour calculer le temps de construction en fonction du niveau du bâtiment
function calculerTempsConstruction($niveau_batiment, $temps_base) {
    return $temps_base * pow(1.5, $niveau_batiment - 1);
}

// Vérifier si l'utilisateur a une construction en cours
$sql_construction = "SELECT ub.temps_fin, ub.batiment_id, b.nom FROM utilisateur.utilisateur_batiments ub JOIN utilisateur.batiments b ON ub.batiment_id = b.id WHERE ub.utilisateur_id = ? AND ub.temps_fin IS NOT NULL";
$stmt_construction = $conn->prepare($sql_construction);
if ($stmt_construction === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_construction->bind_param("i", $user_id);
$stmt_construction->execute();
$result_construction = $stmt_construction->get_result();
$construction_en_cours = $result_construction->fetch_assoc();

$temps_restant_seconds = null;
$temps_restant_format = null;

if ($construction_en_cours) {
    // Calculer le temps restant en secondes
    $temps_restant_seconds = strtotime($construction_en_cours['temps_fin']) - time();
    $temps_restant_format = gmdate('H:i:s', $temps_restant_seconds);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestions des Bâtiments</title>
    <script>
        function updateTimeRemaining() {
            const timeRemainingElement = document.getElementById('time-remaining');
            let secondsRemaining = parseInt(timeRemainingElement.getAttribute('data-seconds'), 10);

            if (secondsRemaining > 0) {
                secondsRemaining--;
                timeRemainingElement.setAttribute('data-seconds', secondsRemaining);
                const hours = Math.floor(secondsRemaining / 3600);
                const minutes = Math.floor((secondsRemaining % 3600) / 60);
                const seconds = secondsRemaining % 60;
                timeRemainingElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            } else {
                timeRemainingElement.textContent = "Construction terminée !";
                document.getElementById('unlock-button').style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('time-remaining')) {
                setInterval(updateTimeRemaining, 1000);
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Gestions des Bâtiments</h1>

        <?php if ($construction_en_cours): ?>
            <h2>Construction en cours</h2>
            <p>Vous êtes actuellement en train de construire le bâtiment <strong><?php echo htmlspecialchars($construction_en_cours['nom']); ?></strong>.</p>
            <p>Temps restant : <strong id="time-remaining" data-seconds="<?php echo $temps_restant_seconds; ?>"><?php echo $temps_restant_format; ?></strong> (heures:minutes:secondes).</p>
            <form method="post" action="deblocage_batiment.php" id="unlock-button" style="display: none;">
                <input type="hidden" name="batiment_id" value="<?php echo $construction_en_cours['batiment_id']; ?>">
                <button type="submit">Débloquer le bâtiment</button>
            </form>
        <?php else: ?>
            <h2>Bâtiments disponibles</h2>
            <table>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Coût de construction</th>
                    <th>Temps de construction</th>
                    <th>Niveau actuel</th>
                    <th>Action</th>
                </tr>
                <?php
                // Récupérer les bâtiments disponibles avec le niveau le plus élevé
                $sql_batiments = "
                    SELECT b.*, COALESCE(MAX(ub.niveau), 0) AS niveau_actuel
                    FROM utilisateur.batiments b
                    LEFT JOIN utilisateur.utilisateur_batiments ub ON b.id = ub.batiment_id AND ub.utilisateur_id = ?
                    GROUP BY b.id";
                $stmt_batiments = $conn->prepare($sql_batiments);
                $stmt_batiments->bind_param("i", $user_id);
                $stmt_batiments->execute();
                $result_batiments = $stmt_batiments->get_result();
                while ($batiment = $result_batiments->fetch_assoc()):
                    if (isset($batiment['temps_construction'])):
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($batiment['nom']); ?></td>
                        <td><?php echo htmlspecialchars($batiment['description']); ?></td>
                        <td><?php echo htmlspecialchars($batiment['cout_construction']); ?></td>
                        <td><?php echo htmlspecialchars(calculerTempsConstruction($batiment['niveau_actuel'], $batiment['temps_construction'])); ?> heures</td>
                        <td><?php echo htmlspecialchars($batiment['niveau_actuel']); ?></td>
                        <td><a href="construire_batiment.php?id=<?php echo $batiment['id']; ?>">Construire</a></td>
                    </tr>
                <?php
                    else:
                ?>
                    <tr>
                        <td colspan="6">Données manquantes pour le bâtiment <?php echo htmlspecialchars($batiment['nom']); ?>.</td>
                    </tr>
                <?php
                    endif;
                endwhile;
                $stmt_batiments->close();
                ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$stmt_construction->close();
$conn->close();
?>