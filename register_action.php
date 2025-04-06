<?php
session_start();
include('db2.php'); // Connexion à la base de données

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO utilisateur (nom_utilisateur, email, mot_de_passe) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);
    $stmt->execute();

    $_SESSION['user'] = $username;
    header('Location: dashboard.php');
}
?>
