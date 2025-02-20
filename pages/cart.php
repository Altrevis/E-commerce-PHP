<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user'])) {
    header('Location: /pages/login.php');
    exit;
}

if (isset($_POST['article_id']) && isset($_POST['quantity'])) {
    $article_id = (int) $_POST['article_id'];
    $quantity = (int) $_POST['quantity'];
    $user_id = $_SESSION['user']['id'];

    // Vérifier le stock disponible
    $stmt = $pdo->prepare("SELECT quantity FROM stock WHERE article_id = ?");
    $stmt->execute([$article_id]);
    $stock_available = $stmt->fetchColumn();

    // Vérifier la quantité déjà présente dans le panier
    $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND article_id = ?");
    $stmt->execute([$user_id, $article_id]);
    $existing_quantity = $stmt->fetchColumn() ?: 0;

    if ($stock_available === false) {
        $error = "Article non trouvé.";
    } elseif (($existing_quantity + $quantity) > $stock_available) {
        $error = "Stock insuffisant. Il reste seulement $stock_available en stock.";
    } else {
        if ($existing_quantity) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND article_id = ?");
            $stmt->execute([$quantity, $user_id, $article_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, article_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $article_id, $quantity]);
        }
    }
}

if (isset($_POST['cart_id'])) {
    $cart_id = (int) $_POST['cart_id'];
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE id = ?");
    $stmt->execute([$cart_id]);
    $cart_item = $stmt->fetch();

    if ($cart_item) {
        if ($cart_item['quantity'] > 1) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity - 1 WHERE id = ?");
            $stmt->execute([$cart_id]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
            $stmt->execute([$cart_id]);
        }
    }
}

$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT c.id, a.name, a.image_url, c.quantity, a.price FROM cart c JOIN articles a ON c.article_id = a.id WHERE c.user_id = ?");
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
        <?= htmlspecialchars($item['name']) ?> - Quantity: <?= $item['quantity'] ?> - Price:
        $<?= number_format($item['price'], 2) ?>
        <form method="POST" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
            <button type="submit">Remove</button>
        </form>
        <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
<a href="../pages/cart_validate.php">Proceed to Checkout</a>
<?php endif; ?>