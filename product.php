<?php
require 'database.php';
session_start();

if (!isset($_GET['id']) || !isset($_GET['slug'])) {
    die("Article introuvable.");
}

$id = (int) $_GET['id'];
$slug = htmlspecialchars($_GET['slug']);

$stmt = $pdo->prepare("SELECT * FROM ARTICLE WHERE ID = :id AND Slug = :slug");
$stmt->execute(['id' => $id, 'slug' => $slug]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    die("Article non trouvé.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['Nom']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($article['Nom']); ?></h1>
        <img src="<?= htmlspecialchars($article['ImageLink']); ?>" alt="<?= htmlspecialchars($article['Nom']); ?>">
        <p><?= nl2br(htmlspecialchars($article['Description'])); ?></p>
        <p><strong>Publié le:</strong> <?= $article['DatePublication']; ?></p>
        
        <form action="().php" method="POST">
            <input type="hidden" name="article_id" value="<?= $article['ID']; ?>">
            <button type="submit">Ajouter au panier</button>
        </form>

        <a href="index.php">Retour à l'accueil</a>
    </div>
</body>
</html>