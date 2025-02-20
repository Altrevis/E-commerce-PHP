<?php
session_start();
require_once '../includes/db.php';

// Vérification de l'authentification et des permissions
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit;
}

// Vérification de la présence de l'ID de l'article
if (!isset($_GET['article_id'])) {
    die("No article specified.");
}

$article_id = $_GET['article_id'];
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

// Vérification de l'existence de l'article
if (!$article) {
    die("Article not found.");
}

// Génération du token CSRF si absent
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Traitement du formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $name = $_POST['name'];
    $content = $_POST['content'];

    $stmt = $pdo->prepare("UPDATE articles SET name = ?, content = ? WHERE id = ?");
    $stmt->execute([$name, $content, $article_id]);

    header("Location: /pages/admin.php?success=article_updated");
    exit;
}
?>

<h1>Edit Article</h1>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <label for="name">Title:</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($article['name']) ?>" required>

    <label for="content">Content:</label>
    <textarea id="content" name="content" required><?= htmlspecialchars($article['content']) ?></textarea>

    <button type="submit">Save Changes</button>
</form>
