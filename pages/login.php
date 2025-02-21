<?php
session_start();  // Démarre la session PHP
require_once '../includes/db.php';  // Inclut le fichier de connexion à la base de données
require_once '../includes/header.php';  // Inclut l'en-tête de la page (header)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // Vérifie si la requête est de type POST (formulaire soumis)
    $username = $_POST['username'];  // Récupère le nom d'utilisateur envoyé via le formulaire
    $password = $_POST['password'];  // Récupère le mot de passe envoyé via le formulaire

    // Prépare et exécute la requête pour rechercher l'utilisateur par son nom d'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();  // Récupère les informations de l'utilisateur si elles existent

    // Vérifie si l'utilisateur existe et si le mot de passe est correct
    if ($user && password_verify($password, $user['password'])) {
        // Si la connexion est réussie, stocke l'utilisateur dans la session
        $_SESSION['user'] = $user;
        $_SESSION['username'] = $user['username'];  // Stocke également le nom d'utilisateur dans la session

        // Si l'utilisateur est un admin, redirige vers la page d'admin
        if ($user['role'] === 'admin') {
            header('Location: ./admin.php');  // Redirection vers la page admin
            exit;
        } else {
            // Sinon, redirige vers la page d'accueil
            header('Location: ./index.php');  // Redirection vers la page d'accueil
            exit;
        }
    } else {
        // Si l'utilisateur n'existe pas ou le mot de passe est incorrect
        echo "Invalid username or password.";  // Message d'erreur
    }
}
?>

<!-- Formulaire de connexion -->
<form method="POST">
    <!-- Formulaire envoyé en méthode POST -->
    <input type="text" name="username" placeholder="Username" required> <!-- Champ pour le nom d'utilisateur -->
    <input type="password" name="password" placeholder="Password" required> <!-- Champ pour le mot de passe -->
    <button type="submit">Login</button> <!-- Bouton de soumission -->
</form>

<?php
// Si l'utilisateur est déjà connecté et est un admin, afficher un bouton pour accéder à l'admin
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    // Si l'utilisateur a un rôle admin, afficher le bouton pour accéder au panneau d'administration
    echo '<a href="/pages/admin.php"><button type="button">Go to Admin Panel</button></a>';
}
?>