<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT CART.ID AS CartID, ARTICLE.ID, ARTICLE.Nom, ARTICLE.ImageLink, ARTICLE.TotalAmount, CART.Quantity
    FROM CART c
    JOIN ARTICLE a ON c.ArticleID = a.ID
    WHERE c.UserID = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['TotalAmount'] * $item['Quantity'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Panier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Votre Panier</h1>
        
        <?php if (empty($cartItems)): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($item['ImageLink']) ?>" width="50"></td>
                            <td><?= htmlspecialchars($item['Nom']) ?></td>
                            <td><?= number_format($item['TotalAmount'], 2) ?>€</td>
                            <td><?= $item['Quantity'] ?></td>
                            <td><?= number_format($item['TotalAmount'] * $item['Quantity'], 2) ?>€</td>
                            <td>
                                <form action="updateCart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="cart_id" value="<?= $item['CartID'] ?>">
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit">+</button>
                                </form>
                                <form action="updateCart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="cart_id" value="<?= $item['CartID'] ?>">
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit">-</button>
                                </form>
                                <form action="removeCart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="cart_id" value="<?= $item['CartID'] ?>">
                                    <button type="submit">❌</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p><strong>Total:</strong> <?= number_format($total, 2) ?>€</p>

            <form action="checkout.php" method="POST">
                <button type="submit">Passer commande</button>
            </form>
        <?php endif; ?>

        <a href="index.php">Retour à la boutique</a>
    </div>
</body>
</html>
