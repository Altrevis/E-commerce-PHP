<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

// Fetch all articles
$stmt = $pdo->query("SELECT * FROM articles ORDER BY published_at DESC");
$articles = $stmt->fetchAll();
?>

<h1>Product List</h1>

<?php if (!empty($_SESSION["user"]) && $_SESSION["user"]["role"] === "admin"): ?>
    <a href="/pages/admin.php" class="admin-btn">Admin</a>
<?php endif; ?>

<?php if (empty($articles)): ?>
    <p>No products available.</p>
<?php else: ?>
    <ul class="product-list">
        <?php foreach ($articles as $article): ?>
            <li class="product-item">
                <a href="/pages/product_detail.php?id=<?= htmlspecialchars($article['id']) ?>">
                    <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['name']) ?>" width="150">
                    <h2><?= htmlspecialchars($article['name']) ?></h2>
                    <p><?= htmlspecialchars($article['description']) ?></p>
                    <small>Published on: <?= htmlspecialchars($article['published_at']) ?></small>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
