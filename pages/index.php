<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

// Récupère tous les articles depuis la base de données, triés par date de publication (du plus récent au plus ancien)
$stmt = $pdo->query("SELECT a.*, u.username, u.id as user_id FROM articles a JOIN users u ON a.user_id = u.id ORDER BY a.published_at DESC");
$articles = $stmt->fetchAll();
?>

<h1>Product List</h1>

<?php if (empty($articles)): ?>
<!-- Si aucun produit n'est disponible -->
<p>No products available.</p>
<?php else: ?>
<ul class="product-list">
    <?php foreach ($articles as $article): ?>
    <li class="product-item">
        <!-- Lien vers la page de détails du produit -->
        <a href="product_detail.php?id=<?= htmlspecialchars($article['id']) ?>">
            <!-- Affichage de l'image de l'article -->
            <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['name']) ?>"
                width="150">
            <!-- Affichage du nom de l'article -->
            <h2><?= htmlspecialchars($article['name']) ?></h2>
            <!-- Affichage de la description de l'article -->
            <p><?= htmlspecialchars($article['description']) ?></p>
            <!-- Affichage du prix de l'article -->
            <p><strong>Price: $<?= number_format($article['price'], 2) ?></strong></p>
            <!-- Affichage de la date de publication de l'article -->
            <p>Published on: <?= htmlspecialchars($article['published_at']) ?></p>
            <!-- Affichage du nom de l'utilisateur qui a créé l'article -->
            <small>Created by:</small>
            <small>
                <button class="username-button">
                    <!-- Lien vers le profil de l'utilisateur qui a publié l'article -->
                    <a href="profile.php?id=<?= htmlspecialchars($article['user_id']) ?>">
                        <?= htmlspecialchars($article['username']) ?>
                    </a>
                </button>
            </small>
        </a>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>