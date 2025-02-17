<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO USER (Username, Password) VALUES (:username, :password)");
    try {
        $stmt->execute(['username' => $username, 'password' => $password]);
        header('Location: login.html');
        exit;
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erreur lors de l'inscription.</p>";
    }
}
?>