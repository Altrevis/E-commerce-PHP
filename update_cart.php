<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté.");
}

$user_id = $_SESSION['user_id'];
$article_id = (int)$_POST['article_id'];

if (isset($_POST['increase'])) {
    $stmt = $pdo->prepare("SELECT Quantity FROM STOCK WHERE ArticleID = :article_id");
    $stmt->execute(['article_id' => $article_id]);
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stock && $stock['Quantity'] > 0) {
        $stmt = $pdo->prepare("INSERT INTO CART (UserID, ArticleID) VALUES (:user_id, :article_id)");
        $stmt->execute(['user_id' => $user_id, 'article_id' => $article_id]);

        $stmt = $pdo->prepare("UPDATE STOCK SET Quantity = Quantity - 1 WHERE ArticleID = :article_id");
        $stmt->execute(['article_id' => $article_id]);
    }
} elseif (isset($_POST['decrease'])) {
    $stmt = $pdo->prepare("DELETE FROM CART WHERE UserID = :user_id AND ArticleID = :article_id LIMIT 1");
    $stmt->execute(['user_id' => $user_id, 'article_id' => $article_id]);

    $stmt = $pdo->prepare("UPDATE STOCK SET Quantity = Quantity + 1 WHERE ArticleID = :article_id");
    $stmt->execute(['article_id' => $article_id]);
}

header("Location: cart.php");
exit();
