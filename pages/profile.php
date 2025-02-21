<?php
session_start();  // Démarre la session PHP pour maintenir les informations de l'utilisateur connecté

require_once '../includes/db.php';  // Inclut la connexion à la base de données
require_once '../includes/header.php';  // Inclut le fichier d'en-tête (menu et liens)

$user_id = $_GET['id'];  // Récupère l'ID de l'utilisateur à partir de l'URL

// Prépare la requête pour récupérer les informations de l'utilisateur à partir de l'ID
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();  // Récupère les informations de l'utilisateur

// Prépare la requête pour récupérer les articles créés par cet utilisateur
$stmt = $pdo->prepare("SELECT * FROM articles WHERE user_id = ?");
$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();  // Récupère tous les articles créés par cet utilisateur
?>

<h1>Profile of <?= htmlspecialchars($user['username']) ?></h1> <!-- Affiche le nom d'utilisateur -->

<h3>Email: <?= htmlspecialchars($user['email']) ?></h3> <!-- Affiche l'email de l'utilisateur -->

<h2>Articles created by <?= htmlspecialchars($user['username']) ?></h2> <!-- Affiche l'en-tête "Articles créés par" -->

<?php
$stmt = $pdo->prepare("SELECT * FROM articles WHERE user_id = ?");
$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();  // Récupère à nouveau les articles créés par l'utilisateur
?>

<?php if (empty($articles)): ?>
<!-- Vérifie si l'utilisateur n'a aucun article -->
<p>No products available.</p> <!-- Affiche un message si l'utilisateur n'a pas d'articles -->
<?php else: ?>
<ul class="product-list">
    <!-- Liste des articles -->
    <?php foreach ($articles as $article): ?>
    <!-- Pour chaque article créé par l'utilisateur -->
    <li class="product-item">
        <a href="product_detail.php?id=<?= htmlspecialchars($article['id']) ?>">
            <!-- Lien vers la page de détail de l'article -->
            <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['name']) ?>"
                width="150"> <!-- Affiche l'image de l'article -->
            <h2><?= htmlspecialchars($article['name']) ?></h2> <!-- Affiche le nom de l'article -->
            <p><?= htmlspecialchars($article['description']) ?></p> <!-- Affiche la description de l'article -->
            <p><strong>Price: $<?= number_format($article['price'], 2) ?></strong></p>
            <!-- Affiche le prix formaté de l'article -->
            <small>Published on: <?= htmlspecialchars($article['published_at']) ?></small>
            <!-- Affiche la date de publication de l'article -->
        </a>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>