<?php
session_start();
require_once '../includes/db.php';

// Vérifier si l'utilisateur est connecté et administrateur
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'user')) {
    header('Location: /pages/login.php');
    exit;
}

// Vérifier si le token CSRF est valide
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token.");
}

// Vérifier si le formulaire a été soumis pour supprimer un utilisateur
if (isset($_POST['user_id'])) {
    // Vérifier que l'utilisateur est un admin avant de supprimer
    if ($_SESSION['user']['role'] === 'admin') {
        $user_id = $_POST['user_id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        header('Location: /admin.php');
        exit;
    } else {
        die("You do not have permission to delete this user.");
    }
}

// Vérifier si le formulaire a été soumis pour supprimer un article
if (isset($_POST['article_id'])) {
    $article_id = $_POST['article_id'];
    $user_id = $_SESSION['user']['id'];

    // Vérifier si l'utilisateur est l'auteur de l'article ou un administrateur
    $stmt = $pdo->prepare("SELECT user_id FROM articles WHERE id = ?");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch();

    if (!$article) {
        die("Article not found.");
    }

    // Si l'utilisateur est l'auteur de l'article ou un administrateur
    if ($article['user_id'] === $user_id || $_SESSION['user']['role'] === 'admin') {
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$article_id]);
        header('Location: /admin.php');
        exit;
    } else {
        die("You do not have permission to delete this article.");
    }
}
?>
