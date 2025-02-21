<?php
session_start();  // Démarre la session PHP
require_once '../includes/db.php';  // Inclut le fichier de connexion à la base de données
require_once '../includes/header.php';  // Inclut l'en-tête de la page (header)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prépare et exécute la requête pour rechercher l'utilisateur par son email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Vérifie si l'utilisateur existe et si le mot de passe est correct
    if ($user && password_verify($password, $user['password'])) {
        // Si la connexion est réussie, stocke l'utilisateur dans la session
        $_SESSION['user'] = $user;
        $_SESSION['username'] = $user['username'];

        // Si l'utilisateur est un admin, redirige vers la page d'admin
        if ($user['role'] === 'admin') {
            header('Location: ./admin.php');
            exit;
        } else {
            // Sinon, redirige vers la page d'accueil
            header('Location: ./index.php');
            exit;
        }
    } else {
        // Si l'utilisateur n'existe pas ou le mot de passe est incorrect
        echo "Invalid email or password.";
    }
}
?>

<!-- Formulaire de connexion -->
<form method="POST">
    <input type="email" name="email" placeholder="Email" required> <!-- Champ pour le nom d'utilisateur -->
    <input type="password" name="password" placeholder="Password" required><!-- Champ pour le mot de passe -->
    <button type="submit">Login</button> <!-- Bouton de soumission -->
</form>
<?php
// Si l'utilisateur est déjà connecté et est un admin, afficher un bouton pour accéder à l'admin
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    // Si l'utilisateur a un rôle admin, afficher le bouton pour accéder au panneau d'administration
    echo '<a href="/pages/admin.php"><button type="button">Go to Admin Panel</button></a>';
}
?>