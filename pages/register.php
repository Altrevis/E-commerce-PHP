<?php
session_start();  // Démarre la session PHP pour pouvoir y stocker des informations utilisateur

require_once '../includes/db.php';  // Inclut la connexion à la base de données
require_once '../includes/header.php';  // Inclut l'en-tête pour la navigation et autres éléments

// Vérifie si la méthode de requête est POST (formulaire soumis)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère les données envoyées par le formulaire
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Sécurise le mot de passe avec un hachage

    // Insère l'utilisateur dans la base de données, avec une balance initiale à 0
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, balance) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, 0]);

    // Récupère l'ID de l'utilisateur inséré
    $user_id = $pdo->lastInsertId();

    // Stocke toutes les informations nécessaires de l'utilisateur dans la session
    $_SESSION['user'] = [
        'id' => $user_id,
        'username' => $username,
        'email' => $email,
        'balance' => 0  // Initialisation de la balance à 0
    ];

    // Redirige l'utilisateur vers la page d'accueil après l'inscription
    header('Location: ./index.php');
    exit;  // Termine l'exécution du script après la redirection
}
?>

<!-- Formulaire d'inscription -->
<form method="POST">
    <input type="text" name="username" placeholder="Username" required> <!-- Champ pour le nom d'utilisateur -->
    <input type="email" name="email" placeholder="Email" required> <!-- Champ pour l'email -->
    <input type="password" name="password" placeholder="Password" required> <!-- Champ pour le mot de passe -->
    <button type="submit">Register</button> <!-- Bouton de soumission -->
</form>