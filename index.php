<?php
require 'database.php';

$stmt = $pdo->prepare("SELECT * FROM ARTICLE");
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Mon site</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>    
    <div class="container">
        <h2>Articles Disponibles</h2>
        
        <div class="articles">
            <?php foreach ($articles as $article): ?>
                <div class="article">
                    <img src="<?= htmlspecialchars($article['Image-Link']); ?>" alt="<?= htmlspecialchars($article['Nom']); ?>">
                    <h2><a href="product.php?id=<?= $article['ID']; ?>&slug=<?= $article['Slug']; ?>">
                        <?= htmlspecialchars($article['Nom']); ?>
                    </a></h2>
                    <p><?= htmlspecialchars($article['Description']); ?></p>
                    <p><strong>Publié le:</strong> <?= $article['DatePublication']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
