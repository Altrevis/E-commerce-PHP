<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: /pages/login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Supprimer un article du panier
    if (isset($_POST['cart_id'])) {
        $cart_id = $_POST['cart_id'];
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);

        header('Location: /pages/cart.php');
        exit;
    }

    // Calculer le total du panier
    $stmt = $pdo->prepare("
        SELECT SUM(a.price * c.quantity) AS total 
        FROM cart c 
        JOIN articles a ON c.article_id = a.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $total = $stmt->fetchColumn();

    if ($_SESSION['user']['balance'] >= $total) {
        // Démarrer la transaction
        $pdo->beginTransaction();

        try {
            // Déduire le solde de l'utilisateur
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$total, $user_id]);

            // Créer une nouvelle commande
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, items) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $total, json_encode([])]); // `json_encode([])` sert de placeholder pour les items

            // Vider le panier
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);

            // Valider la transaction
            $pdo->commit();
            echo "Order placed successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Error placing order: " . $e->getMessage();
        }
    } else {
        echo "Insufficient balance.";
    }
}

// Récupérer le total du panier
$stmt = $pdo->prepare("
    SELECT SUM(a.price * c.quantity) AS total 
    FROM cart c 
    JOIN articles a ON c.article_id = a.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$total = $stmt->fetchColumn();
?>

<h1>Proceed to Checkout</h1>

<p>Total amount: $<?= htmlspecialchars(number_format($total, 2)) ?></p>

<form method="POST">
    <button type="submit">Confirm and Pay</button>
</form>
