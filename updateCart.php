<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = (int) $_POST['cart_id'];
    $action = $_POST['action'];

    if ($action === 'increase') {
        $stmt = $pdo->prepare("UPDATE CART SET Quantity = Quantity + 1 WHERE ID = :cart_id");
    } elseif ($action === 'decrease') {
        $stmt = $pdo->prepare("UPDATE CART SET Quantity = Quantity - 1 WHERE ID = :cart_id AND Quantity > 1");
    }

    $stmt->execute(['cart_id' => $cart_id]);

    header('Location: cart.php');
    exit;
}
?>
