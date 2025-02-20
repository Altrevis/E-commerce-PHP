<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

// Fetch all articles from the database, sorted by publication date (newest first)
$stmt = $pdo->query("SELECT a.*, u.username, u.id as user_id FROM articles a JOIN users u ON a.user_id = u.id ORDER BY a.published_at DESC");
$articles = $stmt->fetchAll();
?>

<h1>Product List</h1>

<?php if (empty($articles)): ?>
    <p>No products available.</p>
<?php else: ?>
    <ul class="product-list">
        <?php foreach ($articles as $article): ?>
            <li class="product-item">
                <a href="product_detail.php?id=<?= htmlspecialchars($article['id']) ?>">
                    <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['name']) ?>" width="150">
                    <h2><?= htmlspecialchars($article['name']) ?></h2>
                    <p><?= htmlspecialchars($article['description']) ?></p>
                    <p><strong>Price: $<?= number_format($article['price'], 2) ?></strong></p> <!-- Affichage du prix -->
                    <p>Published on: <?= htmlspecialchars($article['published_at']) ?></p>
                    <small>Created by:</small>
                    <small>
                        <button class="username-button">
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