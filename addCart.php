<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $article_id = (int) $_POST['article_id'];

    $stmt = $pdo->prepare("SELECT * FROM CART WHERE UserID = :user_id AND ArticleID = :article_id");
    $stmt->execute(['user_id' => $user_id, 'article_id' => $article_id]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cartItem) {
        $stmt = $pdo->prepare("UPDATE CART SET Quantity = Quantity + 1 WHERE UserID = :user_id AND ArticleID = :article_id");
        $stmt->execute(['user_id' => $user_id, 'article_id' => $article_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO CART (UserID, ArticleID, Quantity) VALUES (:user_id, :article_id, 1)");
        $stmt->execute(['user_id' => $user_id, 'article_id' => $article_id]);
    }

    header('Location: cart.php');
    exit;
}
?>