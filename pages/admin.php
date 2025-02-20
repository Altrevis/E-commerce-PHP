<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit;
}

// Handle deletion of user
if (isset($_POST['delete_user']) && $_POST['delete_user'] === '1') {
    $user_id = $_POST['user_id'];
    try {
        $pdo->beginTransaction(); // Début de la transaction

        // Supprimer les commandes liées à l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM orders WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Supprimer le panier de l'utilisateur (si applicable)
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Supprimer l'utilisateur de la base de données
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit(); // Valider la transaction

        echo "User deleted successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack(); // Annuler la transaction en cas d'erreur
        echo "Error deleting user: " . $e->getMessage();
    }
}

// Handle deletion of article
if (isset($_POST['delete_article']) && $_POST['delete_article'] === '1') {
    $article_id = $_POST['article_id'];
    try {
        // Delete records from stock table
        $stmt = $pdo->prepare("DELETE FROM stock WHERE article_id = ?");
        $stmt->execute([$article_id]);
        
        // Delete the article from the database
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$article_id]);
        
        // Refresh the articles list
        $articles = $pdo->query("SELECT * FROM articles")->fetchAll();
        echo "Article deleted successfully!";
    } catch (PDOException $e) {
        echo "Error deleting article: " . $e->getMessage();
    }
}

// Fetch all users and articles
$users = $pdo->query("SELECT * FROM users")->fetchAll();
$articles = $pdo->query("SELECT * FROM articles")->fetchAll();
?>


<h1>Admin Panel</h1>

<h2>Users</h2>
<ul>
    <?php foreach ($users as $user): ?>
        <?php if ($user['role'] !== 'admin'): ?>
            <li>
                <?= htmlspecialchars($user['username']) ?> - <?= htmlspecialchars($user['email']) ?>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <input type="hidden" name="delete_user" value="1">
                    <button type="submit">Delete</button>
                </form>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

<h2>Articles</h2>
<ul>
    <?php foreach ($articles as $article): ?>
        <li>
            <?= htmlspecialchars($article['name']) ?>
            <form method="POST" action="" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                <input type="hidden" name="delete_article" value="1">
                <button type="submit">Delete</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>