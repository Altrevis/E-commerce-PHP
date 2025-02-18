<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

define('ADMIN_USER', 'user'); // Identifiant admin
define('ADMIN_PASS', 'password'); // Mot de passe admin

$error = ""; // Stockage des erreurs

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                "id" => $user["id"],
                "username" => $user["username"]
            ];

            // Vérification si c'est l'admin
            if ($username === ADMIN_USER && $password === ADMIN_PASS) {
                $_SESSION['admin_authenticated'] = true;
            }

            header('Location: ./index.php');
            exit;
        } else {
            $error = "Identifiant ou mot de passe incorrect.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Nom d'utilisateur" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit">Connexion</button>
</form>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
