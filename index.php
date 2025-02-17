<?php
require 'database.php';

$query = "SELECT * FROM ARTICLE ORDER BY DatePublication DESC";
$stmt = $pdo->query($query);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Articles</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Liste des Articles</h1>
        <div class="articles">
            <?php if ($articles): ?>
                <?php foreach ($articles as $article): ?>
                    <div class="article">
                        <h2><?php echo htmlspecialchars($article['Nom']); ?></h2>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($article['Description']); ?></p>
                        <p><strong>Date de publication:</strong> <?php echo $article['DatePublication']; ?></p>
                        <img src="<?php echo htmlspecialchars($article['ImageLink']); ?>" alt="<?php echo htmlspecialchars($article['Nom']); ?>" width="200">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun article trouvé.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
