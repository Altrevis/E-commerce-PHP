<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Si la connexion est réussie, on stocke l'utilisateur dans la session
        $_SESSION['user'] = $user;
        $_SESSION['username'] = $user['username'];

        // Si l'utilisateur est un admin, rediriger vers la page d'admin
        if ($user['role'] === 'admin') {
            header('Location: ./admin.php');
            exit;
        } else {
            // Sinon, rediriger vers la page d'accueil
            header('Location: ./index.php');
            exit;
        }
    } else {
        echo "Invalid username or password.";
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>

<?php
// Si l'utilisateur est déjà connecté et est un admin, afficher un bouton pour accéder à l'admin
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    echo '<a href="/pages/admin.php"><button type="button">Go to Admin Panel</button></a>';
}
?>