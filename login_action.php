<?php
session_start();
include('db2.php'); // Connexion à la base de données

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM utilisateur WHERE nom_utilisateur = ? AND mot_de_passe = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
		 $user = $result->fetch_assoc();
        $_SESSION['user'] = $username;
		 $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
    } else {
        echo "Identifiants incorrects.";
    }
}
?>
