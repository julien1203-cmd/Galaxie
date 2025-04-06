<?php
session_start();
include('menu.php');
echo $_SESSION['user'];
echo $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur votre Tableau de bord</h1>
        <p>Vous pouvez voir vos planètes, gérer vos vaisseaux et explorer de nouveaux systèmes !</p>
        <a href="planetes.php">Voir vos planètes</a><br>
        <a href="vaisseaux.php">Voir vos vaisseaux</a><br>
        <a href="systemes.php">Explorer les systèmes</a><br>
    </div>
</body>
</html>
