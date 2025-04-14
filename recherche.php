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

// Fonction pour calculer le temps de développement en fonction du niveau du laboratoire de recherche
function calculerTempsDeveloppement($niveau_laboratoire, $temps_base) {
    return $temps_base / $niveau_laboratoire;
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

// Vérifier si l'utilisateur a une recherche en cours
$sql_recherche = "SELECT r.temps_fin, r.technologie_id, t.nom FROM recherche r JOIN technologie t ON r.technologie_id = t.id WHERE r.utilisateur_id = ?";
$stmt_recherche = $conn->prepare($sql_recherche);
if ($stmt_recherche === false) {
    die('Erreur de préparation de la requête : ' . htmlspecialchars($conn->error));
}
$stmt_recherche->bind_param("i", $user_id);
$stmt_recherche->execute();
$result_recherche = $stmt_recherche->get_result();
$recherche_en_cours = $result_recherche->fetch_assoc();

$temps_restant_seconds = null;
$temps_restant_format = null;

if ($recherche_en_cours) {
    // Calculer le temps restant en secondes
    $temps_restant_seconds = strtotime($recherche_en_cours['temps_fin']) - time();
    $temps_restant_format = gmdate('H:i:s', $temps_restant_seconds);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratoire de recherche</title>
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
                timeRemainingElement.textContent = "Recherche terminée !";
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
        <h1>Laboratoire de recherche</h1>

        <?php if ($recherche_en_cours): ?>
            <h2>Recherche en cours</h2>
            <p>Vous êtes actuellement en train de rechercher la technologie <strong><?php echo htmlspecialchars($recherche_en_cours['nom']); ?></strong>.</p>
            <p>Temps restant : <strong id="time-remaining" data-seconds="<?php echo $temps_restant_seconds; ?>"><?php echo $temps_restant_format; ?></strong> (heures:minutes:secondes).</p>
            <form method="post" action="deblocage_technologie.php" id="unlock-button" style="display: none;">
                <input type="hidden" name="technologie_id" value="<?php echo $recherche_en_cours['technologie_id']; ?>">
                <button type="submit">Débloquer la technologie</button>
            </form>
        <?php else: ?>
            <h2>Technologies disponibles</h2>
            <table>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Coût de recherche</th>
                    <th>Temps de développement</th>
                    <th>Niveau actuel</th>
                    <th>Action</th>
                </tr>
                <?php
                // Récupérer les technologies disponibles
                $sql_technologies = "
                    SELECT t.*, COALESCE(MAX(tu.niveau), 0) AS niveau_actuel
                    FROM technologie t
                    LEFT JOIN utilisateur.technologie_utilisateur tu 
                    ON t.id = tu.technologie_id AND tu.utilisateur_id = ?
                    GROUP BY t.id";
                $stmt_technologies = $conn->prepare($sql_technologies);
                $stmt_technologies->bind_param("i", $user_id);
                $stmt_technologies->execute();
                $result_technologies = $stmt_technologies->get_result();
                while ($technologie = $result_technologies->fetch_assoc()):
                    if (isset($technologie['temps_base'])):
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($technologie['nom']); ?></td>
                        <td><?php echo htmlspecialchars($technologie['description']); ?></td>
                        <td><?php echo htmlspecialchars($technologie['cout_recherche']); ?></td>
                        <td><?php echo htmlspecialchars(calculerTempsDeveloppement($niveau_laboratoire, $technologie['temps_base'])); ?> heures</td>
                        <td><?php echo htmlspecialchars($technologie['niveau_actuel']); ?></td>
                        <td><a href="rechercher_technologie.php?id=<?php echo $technologie['id']; ?>">Rechercher</a></td>
                    </tr>
                <?php
                    else:
                ?>
                    <tr>
                        <td colspan="6">Données manquantes pour la technologie <?php echo htmlspecialchars($technologie['nom']); ?>.</td>
                    </tr>
                <?php
                    endif;
                endwhile;
                $stmt_technologies->close();
                ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$stmt_laboratoire->close();
$stmt_recherche->close();
$conn->close();
?>