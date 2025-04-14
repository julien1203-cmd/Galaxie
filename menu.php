<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav>
    <ul>
        <li><a href="index.php">Accueil</a></li>
        <?php if (isset($_SESSION['user_id'])): // Vérifie si l'utilisateur est connecté ?>
            <li><a href="dashboard.php">Tableau de bord</a></li>
            <li><a href="technologie.php">Technologies</a></li>
            
            <li><a href="batiments.php">Bâtiments</a></li>
            <li><a href="recherche.php">Laboratoire de recherche</a></li> <!-- Lien mis à jour pour le laboratoire de recherche -->
            <li><a href="logout.php">Se déconnecter</a></li>
        <?php else: // L'utilisateur n'est pas connecté ?>
            <li><a href="login.php">Se connecter</a></li>
            <li><a href="register.php">S'inscrire</a></li>
        <?php endif; ?>
    </ul>
</nav>