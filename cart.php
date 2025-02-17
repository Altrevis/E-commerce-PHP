<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter.");
}

$userID = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT ARTICLE.ID, ARTICLE.Nom, ARTICLE.Image-Link, ARTICLE.Description 
    FROM CART
    JOIN ARTICLE ON CART.ArticleID = ARTICLE.ID
    WHERE CART.UserID = :userID
");
$stmt->execute(['userID' => $userID]);
$cartArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <?php if (empty($cartArticles)): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($cartArticles as $article): ?>
                    <li>
                        <img src="<?= htmlspecialchars($article['Image-Link']); ?>" alt="<?= htmlspecialchars($article['Nom']); ?>">
                        <h2><?= htmlspecialchars($article['Nom']); ?></h2>
                        <p><?= htmlspecialchars($article['Description']); ?></p>
                        <form action="removeCart.php" method="POST">
                            <input type="hidden" name="article_id" value="<?= $article['ID']; ?>">
                            <button type="submit">Retirer du panier</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a href="index.php">Retour à l'accueil</a>
    </div>
</body>
</html>