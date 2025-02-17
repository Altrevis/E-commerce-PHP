<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM USER WHERE Username = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['role'] = $user['Role'];
        header('Location: index.html');
        exit;
    } else {
        echo "<p style='color:red;'>Email ou mot de passe incorrect.</p>";
    }
}
?>