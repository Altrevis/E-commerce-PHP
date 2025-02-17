<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter.");
}

$userID = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT Solde FROM USERS WHERE ID = :userID");
$stmt->execute(['userID' => $userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$solde = $user['Solde'] ?? 0;

$stmt = $pdo->prepare("
    SELECT CART.ArticleID, ARTICLE.Nom, ARTICLE.Image-Link, CART.Quantity, COMMANDES.TotalAmount
    FROM CART
    JOIN ARTICLE ON CART.ArticleID = ARTICLE.ID
    WHERE CART.UserID = :userID
");
$stmt->execute(['userID' => $userID]);
$cartArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPanier = 0;
foreach ($cartArticles as $article) {
    $totalPanier += $article['TotalAmount'] * $article['Quantity'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="style.css">
    <script src="cart.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Votre Panier</h1>
        <p><strong>Solde actuel :</strong> <?= number_format($solde, 2) ?> €</p>
        <?php if (empty($cartArticles)): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($cartArticles as $article): ?>
                    <li>
                        <img src="<?= htmlspecialchars($article['Image_Link']); ?>" alt="<?= htmlspecialchars($article['Nom']); ?>">
                        <h2><?= htmlspecialchars($article['Nom']); ?></h2>
                        <p>Prix: <?= number_format($article['TotalAmount'], 2) ?> €</p>
                        <p>Quantité: 
                            <button class="decrease" data-id="<?= $article['ArticleID']; ?>">-</button>
                            <span id="qty-<?= $article['ArticleID']; ?>"><?= $article['Quantite']; ?></span>
                            <button class="increase" data-id="<?= $article['ArticleID']; ?>">+</button>
                        </p>
                        <form action="removeCart.php" method="POST">
                            <input type="hidden" name="article_id" value="<?= $article['ArticleID']; ?>">
                            <button type="submit">Supprimer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Total:</strong> <?= number_format($totalPanier, 2) ?> €</p>
            <form action="order.php" method="POST">
                <button type="submit" <?= ($solde >= $totalPanier) ? '' : 'disabled'; ?>>Passer commande</button>
            </form>
        <?php endif; ?>
        <a href="index.php">Retour à l'accueil</a>
    </div>
</body>
</html>
