<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des identifiants fournis
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Vérification si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Vérification du mot de passe
    if ($user && password_verify($password, $user['password'])) {
        // Stockage des informations utilisateur dans la session
        $_SESSION['user'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'role'     => $user['role']
        ];

        // Redirection en fonction du rôle
        $redirect_url = ($user['role'] === 'admin') ? './admin.php' : './index.php';
        header("Location: $redirect_url");
        exit;
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>

<h1>Login</h1>

<?php if (!empty($error_message)): ?>
    <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>

<?php
// Affichage du bouton d'accès au panel admin si l'utilisateur est connecté et admin
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') :
?>
    <a href="/pages/admin.php"><button type="button">Go to Admin Panel</button></a>
<?php endif; ?>
