<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit;
}

// Vérification du token CSRF
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    // Connexion PDO avec gestion des erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        if (isset($_POST['delete_user']) && $_POST['delete_user'] === '1') {
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
            if (!$user_id) {
                throw new Exception("Invalid user ID.");
            }

            $pdo->beginTransaction();

            // Suppression des commandes et du panier
            $stmt = $pdo->prepare("DELETE FROM orders WHERE user_id = ?");
            $stmt->execute([$user_id]);

            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);

            // Suppression de l'utilisateur
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);

            $pdo->commit();
            $_SESSION['success_message'] = "User deleted successfully!";
        }

        if (isset($_POST['delete_article']) && $_POST['delete_article'] === '1') {
            $article_id = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
            if (!$article_id) {
                throw new Exception("Invalid article ID.");
            }

            // Suppression du stock et de l'article
            $stmt = $pdo->prepare("DELETE FROM stock WHERE article_id = ?");
            $stmt->execute([$article_id]);

            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->execute([$article_id]);

            $_SESSION['success_message'] = "Article deleted successfully!";
        }

        header("Location: admin.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header("Location: admin.php");
        exit;
    }
}

// Création du token CSRF si non existant
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Récupération des utilisateurs et articles
$users = $pdo->query("SELECT * FROM users")->fetchAll();
$articles = $pdo->query("SELECT * FROM articles")->fetchAll();
?>

<div class="admin-panel">
    <h1>Admin Panel</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
    <p style="color:green;"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
    <p style="color:red;"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
    <?php endif; ?>

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