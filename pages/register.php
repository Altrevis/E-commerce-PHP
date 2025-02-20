<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validation des champs
    if (empty($username) || empty($email) || empty($password)) {
        echo "All fields are required.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Hacher le mot de passe
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Insérer l'utilisateur dans la base de données avec une balance initiale à 0
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, balance) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $passwordHash, 0]);

        // Récupérer l'ID de l'utilisateur inséré
        $user_id = $pdo->lastInsertId();

        // Stocker les informations de l'utilisateur dans la session
        $_SESSION['user'] = [
            'id' => $user_id,
            'username' => $username,
            'email' => $email,
            'balance' => 0 // initialisation de la balance à 0
        ];

        header('Location: ./index.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>
