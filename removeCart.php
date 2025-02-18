<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = (int) $_POST['cart_id'];

    $stmt = $pdo->prepare("DELETE FROM CART WHERE ID = :cart_id");
    $stmt->execute(['cart_id' => $cart_id]);

    header('Location: cart.php');
    exit;
}
?>
