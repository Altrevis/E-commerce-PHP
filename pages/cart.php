<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT c.id, a.name, a.image_url, c.quantity FROM cart c JOIN articles a ON c.article_id = a.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();
?>

<h1>Cart</h1>

<?php if (empty($cart_items)): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
    <ul>
        <?php foreach ($cart_items as $item): ?>
            <li>
                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="50">
                <?= htmlspecialchars($item['name']) ?> - Quantity: <?= $item['quantity'] ?>
                <form method="POST" action="./cart_validate.php">
                    <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                    <button type="submit">Remove</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="./cart_validate.php">Proceed to Checkout</a>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
