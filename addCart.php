<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['article_id'])) {
    $userID = $_SESSION['user_id'];
    $articleID = (int) $_POST['article_id'];

    $stmt = $pdo->prepare("SELECT * FROM CART WHERE UserID = :userID AND ArticleID = :articleID");
    $stmt->execute(['userID' => $userID, 'articleID' => $articleID]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing) {
        $stmt = $pdo->prepare("INSERT INTO CART (UserID, ArticleID) VALUES (:userID, :articleID)");
        $stmt->execute(['userID' => $userID, 'articleID' => $articleID]);
    }

    header("Location: cart.php");
    exit;
}
?>