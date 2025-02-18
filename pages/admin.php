<?php
session_start();
require_once '../includes/db.php';

// Définition des identifiants admin cachés
define('ADMIN_USER', getenv('ADMIN_USER') ?: 'admin');
define('ADMIN_PASS', getenv('ADMIN_PASS') ?: 'superSecret123');

// Vérification de la connexion admin
if (!isset($_SESSION['admin_authenticated'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? 'Post';
        $password = $_POST['password'] ?? 'Post';

        if ($username === ADMIN_USER && $password === ADMIN_PASS) {
            $_SESSION['admin_authenticated'] = true;
            header('Location: admin.php');
            exit;
        } else {
            $error = "Identifiants incorrects.";
        }
    }

    // Afficher le formulaire de connexion si l'admin n'est pas connecté
    ?>
    <form method="POST">
        <label>Utilisateur:</label>
        <input type="text" name="username" required>
        <label>Mot de passe:</label>
        <input type="password" name="password" required>
        <button type="submit">Se connecter</button>
    </form>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php
    exit;
}

// Fetch all users and articles
$users = $pdo->query("SELECT * FROM users")->fetchAll();
$articles = $pdo->query("SELECT * FROM articles")->fetchAll();
?>

<h1>Admin Panel</h1>
<a href="logout.php">Déconnexion</a>

<h2>Users</h2>
<ul>
    <?php foreach ($users as $user): ?>
        <li>
            <?= htmlspecialchars($user['username']) ?> - <?= htmlspecialchars($user['email']) ?>
            <form method="POST" action="/admin_delete.php">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                <button type="submit">Delete</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>

<h2>Articles</h2>
<ul>
    <?php foreach ($articles as $article): ?>
        <li>
            <?= htmlspecialchars($article['name']) ?>
            <form method="POST" action="/admin_delete.php">
                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                <button type="submit">Delete</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>

<?php require_once '../includes/footer.php'; ?>
