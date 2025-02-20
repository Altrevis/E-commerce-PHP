<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

// Vérification de l'authentification et des permissions
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit;
}

// Suppression d'un utilisateur
if (isset($_POST['delete_user']) && $_POST['delete_user'] === '1') {
    $user_id = $_POST['user_id'];

    try {
        $pdo->beginTransaction(); // Début de la transaction

        // Suppression des commandes de l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM orders WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Suppression du panier de l'utilisateur (si applicable)
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Suppression de l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit(); // Validation de la transaction
        echo "User deleted successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack(); // Annulation en cas d'erreur
        echo "Error deleting user: " . $e->getMessage();
    }
}

// Suppression d'un article
if (isset($_POST['delete_article']) && $_POST['delete_article'] === '1') {
    $article_id = $_POST['article_id'];

    try {
        // Suppression des enregistrements associés dans la table stock
        $stmt = $pdo->prepare("DELETE FROM stock WHERE article_id = ?");
        $stmt->execute([$article_id]);

        // Suppression de l'article
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$article_id]);

        // Rafraîchissement de la liste des articles
        $articles = $pdo->query("SELECT * FROM articles")->fetchAll();
        echo "Article deleted successfully!";
    } catch (PDOException $e) {
        echo "Error deleting article: " . $e->getMessage();
    }
}

// Récupération des utilisateurs et des articles
$users = $pdo->query("SELECT * FROM users")->fetchAll();
$articles = $pdo->query("SELECT * FROM articles")->fetchAll();
?>

<div class="admin-panel">
    <h1>Admin Panel</h1>

    <h2>Users</h2>
    <div class="user-list">
        <?php foreach ($users as $user): ?>
            <?php if ($user['role'] !== 'admin'): ?>
                <div class="user-item">
                    <h2><?= htmlspecialchars($user['username']) ?> - <?= htmlspecialchars($user['email']) ?></h2>

                    <div class="article-list">
                        <?php foreach ($articles as $article): ?>
                            <?php if ($article['user_id'] == $user['id']): ?>
                                <div class="article-item">
                                    <h3><?= htmlspecialchars($article['name']) ?></h3>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                        <input type="hidden" name="delete_article" value="1">
                                        <button type="submit">Delete</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="delete_user" value="1">
                        <button type="submit">Delete</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
