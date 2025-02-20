<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user'])) {
    header('Location: /pages/login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Supprimer l'article du panier
    if (isset($_POST['cart_id'])) {
        $cart_id = $_POST['cart_id'];
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);
        header('Location: /pages/cart.php');
        exit;
    }

    // Traiter le checkout
    $stmt = $pdo->prepare("SELECT SUM(a.price * c.quantity) AS total FROM cart c JOIN articles a ON c.article_id = a.id WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $total = $stmt->fetchColumn();

    if ($_SESSION['user']['balance'] >= $total) {
        // Retirer l'argent et créer la commande
        $pdo->beginTransaction();
        try {
            // Mise à jour de la balance de l'utilisateur (retrait d'argent)
            $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?")->execute([$total, $user_id]);

            // Créer la commande
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, items) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $total, json_encode([])]); // Je laisse `json_encode([])` comme placeholder pour les items de la commande

            // Vider le panier
            $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);

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
?>

<h1>Proceed to Checkout</h1>

<?php
$stmt = $pdo->prepare("SELECT SUM(a.price * c.quantity) AS total FROM cart c JOIN articles a ON c.article_id = a.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$total = $stmt->fetchColumn();
?>

<p>Total amount: $<?= htmlspecialchars(number_format($total, 2)) ?></p>

<form method="POST">
    <button type="submit">Confirm and Pay</button>
</form>