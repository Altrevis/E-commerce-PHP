<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit;
}

// Fetch all users and articles
$users = $pdo->query("SELECT * FROM users")->fetchAll();
$articles = $pdo->query("SELECT * FROM articles")->fetchAll();
?>

<h1>Admin Panel</h1>

<h2>Users</h2>
<ul>
    <?php foreach ($users as $user): ?>
        <li>
            <?= htmlspecialchars($user['username']) ?> - <?= htmlspecialchars($user['email']) ?>
            <form method="POST" action="/admin.php">
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
            <form method="POST" action="/admin.php">
                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                <button type="submit">Delete</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>

<?php require_once '../includes/footer.php'; ?>