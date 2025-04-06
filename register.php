<?php
include('menu.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <div class="container">
        <h1>Créer un compte</h1>
        <form method="post" action="register_action.php">
            <div>
                <label for="username">Nom d'utilisateur:</label>
                <input type="text" name="username" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" required>
            </div>
            <div>
                <label for="password">Mot de passe:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">S'inscrire</button>
        </form>
    </div>
</body>
</html>
