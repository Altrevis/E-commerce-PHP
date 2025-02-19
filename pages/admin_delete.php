<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token.");
}

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    header('Location: /pages/admin.php?success=user_deleted');
    exit;
}

if (isset($_POST['article_id'])) {
    $article_id = $_POST['article_id'];

    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$article_id]);

    header('Location: /pages/admin.php?success=article_deleted');
    exit;
}
?>
