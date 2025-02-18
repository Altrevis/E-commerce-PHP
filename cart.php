<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT Solde FROM USER WHERE ID = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$solde = $user['Solde'];

$stmt = $pdo->prepare("
    SELECT CART.ID AS CartID, ARTICLE.ID AS ArticleID, ARTICLE.Nom, ARTICLE.ImageLink, 
           ARTICLE.Description, COUNT(CART.ArticleID) AS Quantity, STOCK.Quantity AS StockQuantity
    FROM CART
    JOIN ARTICLE ON CART.ArticleID = ARTICLE.ID
    JOIN STOCK ON ARTICLE.ID = STOCK.ArticleID
    WHERE CART.UserID = :user_id
    GROUP BY ARTICLE.ID
");
$stmt->execute(['user_id' => $user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['Quantity'];
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Votre Panier</h1>
        <p>Solde actuel : <?= number_format($solde, 2) ?> €</p>

        <?php if (empty($cart_items)): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Quantité</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($item['ImageLink']); ?>" width="50"></td>
                            <td><?= htmlspecialchars($item['Nom']); ?></td>
                            <td><?= htmlspecialchars($item['Description']); ?></td>
                            <td><?= $item['Quantity']; ?></td>
                            <td><?= $item['StockQuantity']; ?></td>
                            <td>
                                <form action="update_cart.php" method="POST">
                                    <input type="hidden" name="article_id" value="<?= $item['ArticleID']; ?>">
                                    <button type="submit" name="increase">+</button>
                                    <button type="submit" name="decrease">-</button>
                                </form>
                                <form action="remove_from_cart.php" method="POST">
                                    <input type="hidden" name="cart_id" value="<?= $item['CartID']; ?>">
                                    <button type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p>Total à payer : <?= number_format($total_amount, 2) ?> €</p>

            <?php if ($solde >= $total_amount): ?>
                <form action="checkout.php" method="POST">
                    <button type="submit">Passer la commande</button>
                </form>
            <?php else: ?>
                <p style="color: red;">Solde insuffisant pour passer la commande.</p>
            <?php endif; ?>

        <?php endif; ?>

        <a href="index.php">Retour à l'accueil</a>
    </div>
</body>
</html>
