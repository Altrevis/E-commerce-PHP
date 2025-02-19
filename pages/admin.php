<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';
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
            <form method="POST" action="/admin_delete.php" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
            <form method="POST" action="/admin_delete.php" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                <button type="submit">Delete</button>
            </form>
            <a href="/admin_edit.php?article_id=<?= $article['id'] ?>">
                <button>Edit</button>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
